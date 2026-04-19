<?php

use App\Services\Share\MessageService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            $contextKey = match (true) {
                $request->is('member/*') => 'upload.member_avatar.max_size',
                default => 'upload.image.max_size',
            };
            $maxUploadSizeMB = round((config($contextKey, 5120)) / 1024, 2);
            $message = '上傳檔案過大，請選擇小於 ' . $maxUploadSizeMB . 'MB 的檔案後重試。';

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $message,
                ], 413);
            }

            if ($request->is('admin/*')) {
                MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $message);

                $referer = (string) $request->headers->get('referer', '');
                $refererPath = (string) parse_url($referer, PHP_URL_PATH);
                $targetPath = '/admin/';

                # 優先導回後台來源頁，避免回到前台首頁
                if ($refererPath !== '' && str_starts_with($refererPath, '/admin')) {
                    $targetPath = $refererPath;
                } else {
                    # 若無 referer，導回本次 admin 路徑（通常是編輯頁）
                    $targetPath = '/' . ltrim($request->path(), '/');
                }

                $separator = Str::contains($targetPath, '?') ? '&' : '?';

                return redirect($targetPath . $separator . 'upload_error=too_large');
            }

            if ($request->is('member/*')) {
                MessageService::setMessage(MEMBER_MESSAGE_SESSION, MessageService::DANGER, $message);

                $referer = (string) $request->headers->get('referer', '');
                $refererPath = (string) parse_url($referer, PHP_URL_PATH);
                $targetPath = 'member/profile';

                # 優先導回前台會員來源頁，避免回到首頁
                if ($refererPath !== '' && str_starts_with($refererPath, '/member')) {
                    $targetPath = ltrim($refererPath, '/');
                }

                return redirect($targetPath)->withErrors(['upload' => $message]);
            }

            return redirect()->back()->withErrors(['upload' => $message]);
        });
    })->create();

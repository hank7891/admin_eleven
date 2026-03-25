<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Admin\AdminMenuService;
use App\Services\Share\MessageService;

class AdminCheckPermission
{
    /**
     * 檢查當前角色是否有權限存取該頁面
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $menuIds = session(ADMIN_PERMISSION_SESSION, []);

        # 無權限資料時導回首頁
        if (empty($menuIds)) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, '您無權限存取此頁面！');
            return redirect('admin/');
        }

        # 取得允許的 URL 清單（從 session 快取或即時查詢）
        $allowedUrls = session('admin_allowed_urls');
        if (empty($allowedUrls)) {
            $menuService = app(AdminMenuService::class);
            $allowedUrls = $menuService->fetchUrlsByMenuIds($menuIds);
            session(['admin_allowed_urls' => $allowedUrls]);
        }

        # 比對當前路徑是否在允許範圍內（模組前綴匹配）
        $currentPath = $request->path();
        $hasPermission = false;

        foreach ($allowedUrls as $url) {
            $urlPath = ltrim($url, '/');
            # 取得模組前綴（如 admin/employee/list → admin/employee）
            $parts = explode('/', $urlPath);
            $modulePrefix = implode('/', array_slice($parts, 0, 2));

            if (str_starts_with($currentPath, $modulePrefix)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, '您無權限存取此頁面！');
            return redirect('admin/');
        }

        return $next($request);
    }
}

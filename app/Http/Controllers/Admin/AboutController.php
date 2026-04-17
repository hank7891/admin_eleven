<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AboutPageService;
use App\Services\Admin\AdminLogService;
use App\Services\Share\MessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutController extends Controller
{
    const POST_SESSION = 'about_edit_post';

    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(
        protected AboutPageService $service,
        protected AdminLogService $logService
    ) {
        $this->settingService = app('setting');
    }

    /**
     * 關於我們編輯頁
     */
    public function edit()
    {
        try {
            $data = $this->service->getForEdit();

            if (session(self::POST_SESSION)) {
                $data = session(self::POST_SESSION) + $data;
                session()->forget(self::POST_SESSION);
            }

            $this->settingService->setSetData('data', $data);

            return view('Admin.about.edit', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());

            return redirect('admin/');
        }
    }

    /**
     * 關於我們編輯送出
     */
    public function editDo(Request $request): RedirectResponse
    {
        $maxImageSizeMB = round((config('upload.image.max_size', 5120)) / 1024, 2);

        $validator = Validator::make($request->all(), [
            'hero_title' => ['required', 'string', 'max:100'],
            'hero_subtitle' => ['nullable', 'string', 'max:300'],
            'hero_image' => [
                'nullable',
                'file',
                'max:' . (config('upload.image.max_size') ?? 5120),
                'mimes:' . implode(',', config('upload.image.mimes', [])),
            ],
            'story_title' => ['required', 'string', 'max:100'],
            'story_content' => ['required', 'string', 'max:10000'],
            'mission_title' => ['nullable', 'string', 'max:100'],
            'mission_content' => ['nullable', 'string', 'max:5000'],
            'vision_title' => ['nullable', 'string', 'max:100'],
            'vision_content' => ['nullable', 'string', 'max:5000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-()\s]+$/'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'remove_hero_image' => ['nullable', 'in:0,1'],
        ], [
            'hero_title.required' => 'Hero 主標題為必填欄位',
            'hero_image.max' => 'Hero 圖片大小不可超過 ' . $maxImageSizeMB . 'MB',
            'hero_image.mimes' => '僅支援 jpg、jpeg、png、gif、webp 圖片格式',
            'story_title.required' => '品牌故事標題為必填欄位',
            'story_content.required' => '品牌故事內文為必填欄位',
            'contact_email.email' => '聯絡 Email 格式錯誤',
            'contact_phone.regex' => '聯絡電話僅可輸入數字、+、-、空白、括號',
        ]);

        $post = $request->only([
            'hero_title',
            'hero_subtitle',
            'story_title',
            'story_content',
            'mission_title',
            'mission_content',
            'vision_title',
            'vision_content',
            'contact_email',
            'contact_phone',
            'contact_address',
            'meta_description',
            'remove_hero_image',
        ]);

        if ($validator->fails()) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $validator->errors()->first());

            return redirect('admin/about/edit');
        }

        try {
            $result = $this->service->update($post, $request->file('hero_image'));

            # 記錄操作日誌
            $this->logService->recordUpdate(
                $request,
                'about',
                (int) ($result['new']['id'] ?? 1),
                $result['new']['hero_title'] ?? null,
                $result['old'],
                $result['new'],
                [
                    'hero_title',
                    'hero_subtitle',
                    'hero_image_path',
                    'story_title',
                    'story_content',
                    'mission_title',
                    'mission_content',
                    'vision_title',
                    'vision_content',
                    'contact_email',
                    'contact_phone',
                    'contact_address',
                    'meta_description',
                    'updated_by',
                ]
            );

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');

            return redirect('admin/about/edit');
        } catch (\Exception $e) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());

            return redirect('admin/about/edit');
        }
    }
}



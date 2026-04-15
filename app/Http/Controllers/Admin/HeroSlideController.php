<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminLogService;
use App\Services\Admin\HeroSlideService;
use App\Services\Share\FileUploadService;
use App\Services\Share\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HeroSlideController extends Controller
{
    const POST_SESSION = 'hero_slide_edit_post';

    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(
        protected HeroSlideService $service,
        protected AdminLogService $logService,
        protected FileUploadService $uploadService
    ) {
        $this->settingService = app('setting');
    }

    /**
     * 列表頁面
     */
    public function list(Request $request)
    {
        $filters = $request->only(['keyword', 'is_active']);
        $result = $this->service->fetchPaginatedData($filters);

        $this->settingService->setSetData('data', $result['data']);
        $this->settingService->setSetData('pagination', $result['pagination']);
        $this->settingService->setSetData('filters', $filters);
        $this->settingService->setSetData('statusOptions', config('constants.status'));

        return view('Admin/hero_slide/list', $this->settingService->fetchSetData());
    }

    /**
     * 編輯頁面
     */
    public function edit(int $id)
    {
        try {
            $data = ($id > 0)
                ? $this->service->fetchDataByID($id)
                : [
                    'sort_order' => $this->service->fetchNextSortOrder(),
                    'is_active' => STATUS_INACTIVE,
                ];

            if (session(self::POST_SESSION)) {
                $data = session(self::POST_SESSION) + $data;
                session()->forget(self::POST_SESSION);
            }

            $this->settingService->setSetData('data', $data);

            return view('Admin/hero_slide/edit', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/hero-slide/list');
        }
    }

    /**
     * 編輯實作
     */
    public function editDo(Request $request)
    {
        $id = (int) $request->input('id', 0);
        $isEdit = $id > 0;
        $uploadedImagePath = null;
        $maxImageSizeMB = round((config('upload.image.max_size', 5120)) / 1024, 2);

        $rules = [
            'image'               => [$isEdit ? 'nullable' : 'required', 'file', 'max:' . (config('upload.image.max_size') ?? 5120), 'mimes:' . implode(',', config('upload.image.mimes', []))],
            'image_alt'           => ['nullable', 'string', 'max:200'],
            'eyebrow'             => ['nullable', 'string', 'max:100'],
            'title'               => ['required', 'string', 'max:200'],
            'description'         => ['nullable', 'string', 'max:500'],
            'target_url'          => ['nullable', 'string', 'max:500', function ($attribute, $value, $fail) {
                if (!$this->isValidCtaUrl($value)) {
                    $fail('輪播連結格式錯誤');
                }
            }],
            'sort_order'          => ['required', 'integer', 'min:0'],
            'is_active'           => ['required', 'integer', Rule::in(array_keys(config('constants.status')))],
            'start_at'            => ['required', 'date_format:Y-m-d\TH:i'],
            'end_at'              => ['nullable', 'date_format:Y-m-d\TH:i', 'after_or_equal:start_at'],
        ];

        $validator = Validator::make($request->all(), $rules, [
            'image.required'   => '請上傳輪播圖片',
            'image.max'        => '圖片大小不可超過 ' . $maxImageSizeMB . 'MB',
            'image.mimes'      => '僅支援 jpg、jpeg、png、gif、webp 圖片格式',
            'title.required'   => '主標語為必填欄位',
            'title.max'        => '主標語不可超過 200 個字元',
            'sort_order.min'   => '排序不可小於 0',
            'start_at.required'=> '開始呈現時間為必填欄位',
            'start_at.date_format' => '開始呈現時間格式錯誤',
            'end_at.date_format' => '結束呈現時間格式錯誤',
            'end_at.after_or_equal' => '結束呈現時間不可早於開始呈現時間',
        ]);

        $post = $request->only([
            'id',
            'image_alt',
            'eyebrow',
            'title',
            'description',
            'target_url',
            'sort_order',
            'is_active',
            'start_at',
            'end_at',
        ]);

        if ($validator->fails()) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $validator->errors()->first());
            return redirect('admin/hero-slide/edit/' . $id);
        }

        try {
            $oldData = $id > 0 ? $this->service->fetchDataByID($id) : [];

            # 處理圖片上傳
            if ($request->hasFile('image')) {
                $uploadedImagePath = $this->uploadService->upload($request->file('image'), 'image');
                $post['image_path'] = $uploadedImagePath;
            } elseif (!empty($oldData['image_path'])) {
                $post['image_path'] = $oldData['image_path'];
            }

            if ($id === 0) {
                # 新增
                # 新增流程在後端強制停用，避免繞過前端 disabled 欄位
                $post['is_active'] = STATUS_INACTIVE;
                $id = $this->service->addData($post);

                # 記錄操作日誌
                $this->logService->recordSimple($request, 'hero_slide', 'create', $id, $post['title'] ?? null);
            } else {
                # 編輯
                $this->service->updateData($id, $post);

                # 記錄操作日誌
                $this->logService->recordUpdate(
                    $request,
                    'hero_slide',
                    $id,
                    $oldData['title'] ?? null,
                    $oldData,
                    $post,
                    ['image_path', 'image_alt', 'eyebrow', 'title', 'description', 'target_url', 'sort_order', 'is_active', 'start_at', 'end_at']
                );
            }

            # 成功更新後再刪除舊檔，避免更新失敗時遺失原圖
            if (!empty($uploadedImagePath) && !empty($oldData['image_path']) && $oldData['image_path'] !== $uploadedImagePath) {
                $this->uploadService->delete($oldData['image_path']);
            }

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');
            return redirect('admin/hero-slide/edit/' . $id);
        } catch (\Exception $e) {
            # 若新圖片已上傳但後續流程失敗，回收新檔避免孤檔
            if (!empty($uploadedImagePath)) {
                $this->uploadService->delete($uploadedImagePath);
            }

            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/hero-slide/edit/' . $id);
        }
    }

    /**
     * 刪除
     */
    public function delete(Request $request, int $id)
    {
        try {
            $data = $this->service->fetchDataByID($id);

            $this->service->deleteData($id);

            if (!empty($data['image_path'])) {
                $this->uploadService->delete($data['image_path']);
            }

            # 記錄操作日誌
            $this->logService->recordSimple($request, 'hero_slide', 'delete', $id, $data['title'] ?? null);

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '刪除成功！');
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
        }

        return redirect('admin/hero-slide/list');
    }

    /**
     * 快速切換啟用狀態
     */
    public function toggleActive(Request $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->toggleActive($id);

            # 記錄操作日誌
            $this->logService->recordSimple(
                $request,
                'hero_slide',
                'update',
                $id,
                '切換輪播狀態為' . ($result['is_active_display'] ?? '')
            );

            return response()->json([
                'status' => true,
                'message' => '狀態更新成功',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * 驗證 CTA 連結格式
     */
    protected function isValidCtaUrl(?string $value): bool
    {
        $value = trim((string) $value);

        if ($value === '') {
            return true;
        }

        # 允許站內相對路徑與錨點，阻擋 scheme-relative / 反斜線變形
        if (str_starts_with($value, '/')) {
            if (str_starts_with($value, '//') || str_starts_with($value, '/\\')) {
                return false;
            }

            return (bool) preg_match('/^\/[A-Za-z0-9_\-\/.#?=&%]*$/', $value);
        }

        # 僅允許 http / https 絕對網址
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true);
    }

}


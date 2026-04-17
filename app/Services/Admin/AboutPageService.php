<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AboutPageRepository;
use App\Services\Share\FileUploadService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AboutPageService
{
    # 建構元
    public function __construct(
        protected AboutPageRepository $repository,
        protected FileUploadService $uploadService
    ) {
    }

    /**
     * 後台編輯頁資料契約
     */
    public function getForEdit(): array
    {
        $aboutPage = $this->repository->getSingleton();
        $data = $aboutPage->toArray();

        return [
            'id' => (int) ($data['id'] ?? 1),
            'hero_title' => $data['hero_title'] ?? '',
            'hero_subtitle' => $data['hero_subtitle'] ?? '',
            'hero_image_path' => $data['hero_image_path'] ?? null,
            'hero_image_url' => !empty($data['hero_image_path']) ? Storage::url($data['hero_image_path']) : '',
            'story_title' => $data['story_title'] ?? '',
            'story_content' => $data['story_content'] ?? '',
            'mission_title' => $data['mission_title'] ?? '',
            'mission_content' => $data['mission_content'] ?? '',
            'vision_title' => $data['vision_title'] ?? '',
            'vision_content' => $data['vision_content'] ?? '',
            'contact_email' => $data['contact_email'] ?? '',
            'contact_phone' => $data['contact_phone'] ?? '',
            'contact_address' => $data['contact_address'] ?? '',
            'meta_description' => $data['meta_description'] ?? '',
            'updated_at_display' => !empty($data['updated_at'])
                ? Carbon::parse($data['updated_at'])->format('Y-m-d H:i')
                : '',
            'updater_name' => $data['updater']['name'] ?? '',
        ];
    }

    /**
     * 更新關於我們內容
     *
     * @return array{old: array<string, mixed>, new: array<string, mixed>}
     * @throws \Throwable
     */
    public function update(array $data, ?UploadedFile $heroImage = null): array
    {
        $aboutPage = $this->repository->getSingleton();
        $oldData = $aboutPage->toArray();
        $payload = $this->normalizePayload($data);

        $employee = session(ADMIN_AUTH_SESSION);
        if (!empty($employee['id'])) {
            $payload['updated_by'] = (int) $employee['id'];
        }

        $uploadedImagePath = null;
        $oldImagePathToDelete = null;

        DB::beginTransaction();

        try {
            $shouldRemoveHeroImage = (string) ($data['remove_hero_image'] ?? '0') === '1';

            # 先上傳新圖，成功後才更新資料
            if (!empty($heroImage)) {
                $uploadedImagePath = $this->uploadService->upload($heroImage, 'image');
                $payload['hero_image_path'] = $uploadedImagePath;

                if (!empty($oldData['hero_image_path']) && $oldData['hero_image_path'] !== $uploadedImagePath) {
                    $oldImagePathToDelete = (string) $oldData['hero_image_path'];
                }
            } elseif ($shouldRemoveHeroImage && !empty($oldData['hero_image_path'])) {
                # 僅在未上傳新圖時才執行清圖
                $payload['hero_image_path'] = null;
                $oldImagePathToDelete = (string) $oldData['hero_image_path'];
            }

            $updated = $this->repository->update($payload);

            DB::commit();

            # 更新成功後再刪舊圖，避免失敗時原圖遺失
            if (!empty($oldImagePathToDelete)) {
                $this->uploadService->delete($oldImagePathToDelete);
            }

            Cache::forget('about:page');

            return [
                'old' => $oldData,
                'new' => $updated->toArray(),
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            # 若資料更新失敗，回收新圖避免孤兒檔
            if (!empty($uploadedImagePath)) {
                $this->uploadService->delete($uploadedImagePath);
            }

            throw $e;
        }
    }

    /**
     * 正規化儲存資料
     */
    protected function normalizePayload(array $data): array
    {
        return [
            'hero_title' => $this->normalizeRequiredText($data, 'hero_title', 100),
            'hero_subtitle' => $this->normalizeNullableText($data, 'hero_subtitle', 300),
            'story_title' => $this->normalizeRequiredText($data, 'story_title', 100),
            'story_content' => $this->normalizeRequiredText($data, 'story_content', 10000),
            'mission_title' => $this->normalizeNullableText($data, 'mission_title', 100),
            'mission_content' => $this->normalizeNullableText($data, 'mission_content', 5000),
            'vision_title' => $this->normalizeNullableText($data, 'vision_title', 100),
            'vision_content' => $this->normalizeNullableText($data, 'vision_content', 5000),
            'contact_email' => $this->normalizeNullableText($data, 'contact_email', 255),
            'contact_phone' => $this->normalizeNullableText($data, 'contact_phone', 50),
            'contact_address' => $this->normalizeNullableText($data, 'contact_address', 500),
            'meta_description' => $this->normalizeNullableText($data, 'meta_description', 300),
        ];
    }

    /**
     * 正規化必填文字
     */
    protected function normalizeRequiredText(array $data, string $key, int $maxLength): string
    {
        return mb_substr(trim(strip_tags((string) ($data[$key] ?? ''))), 0, $maxLength);
    }

    /**
     * 正規化可為空文字
     */
    protected function normalizeNullableText(array $data, string $key, int $maxLength): ?string
    {
        $value = mb_substr(trim(strip_tags((string) ($data[$key] ?? ''))), 0, $maxLength);

        return $value === '' ? null : $value;
    }
}




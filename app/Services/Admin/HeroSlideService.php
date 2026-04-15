<?php

namespace App\Services\Admin;

use App\Repositories\Admin\HeroSlideRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroSlideService
{
    # 建構元
    public function __construct(protected HeroSlideRepository $repository)
    {
    }

    /**
     * 取得後台分頁資料
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 20): array
    {
        $paginator = $this->repository->fetchPaginatedData($filters, $perPage);

        $data = [];
        foreach ($paginator->items() as $slide) {
            $data[] = $this->formatData($slide->toArray());
        }

        return [
            'data' => $data,
            'pagination' => $paginator,
        ];
    }

    /**
     * 依照 ID 取得輪播資料
     * @throws \Exception
     */
    public function fetchDataByID(int $id): array
    {
        $data = $this->repository->fetchDataByID($id);

        if (empty($data)) {
            throw new \Exception('查無此輪播資料！');
        }

        return $this->formatData($data, true);
    }

    /**
     * 新增輪播
     * @throws \Exception
     */
    public function addData(array $data): int
    {
        # 新增資料一律預設停用，避免透過前端繞過啟用限制
        $data['is_active'] = STATUS_INACTIVE;
        $data = $this->normalizePayload($data, false);
        $slide = $this->repository->addData($data);

        if (empty($slide->id)) {
            throw new \Exception('新增輪播資料失敗！');
        }

        $this->clearFrontendCache();

        return $slide->id;
    }

    /**
     * 取得下一個排序值
     */
    public function fetchNextSortOrder(): int
    {
        return $this->repository->fetchMaxSortOrder() + 1;
    }

    /**
     * 更新輪播
     */
    public function updateData(int $id, array $data): int
    {
        $data = $this->normalizePayload($data, true);
        $this->repository->updateData($id, $data);
        $this->clearFrontendCache();

        return $id;
    }

    /**
     * 刪除輪播
     */
    public function deleteData(int $id): bool
    {
        $result = $this->repository->deleteData($id);
        $this->clearFrontendCache();

        return $result;
    }

    /**
     * 切換啟用狀態
     * @throws \Exception
     */
    public function toggleActive(int $id): array
    {
        $data = $this->fetchDataByID($id);
        $nextStatus = ((int) ($data['is_active'] ?? STATUS_ACTIVE) === STATUS_ACTIVE)
            ? STATUS_INACTIVE
            : STATUS_ACTIVE;

        $this->repository->updateData($id, [
            'is_active' => $nextStatus,
            'updated_by' => session(ADMIN_AUTH_SESSION)['id'] ?? null,
        ]);

        $this->clearFrontendCache();

        return [
            'id' => $id,
            'is_active' => $nextStatus,
            'is_active_display' => config('constants.status.' . $nextStatus),
        ];
    }

    /**
     * 取得前台生效輪播資料契約
     */
    public function fetchActiveSlides(): array
    {
        return Cache::remember('frontend:hero_slides', now()->addMinutes(5), function () {
            $slides = $this->repository->fetchActiveSlides();
            $data = [];

            foreach ($slides as $slide) {
                $item = $slide->toArray();
                $data[] = [
                    'image' => !empty($item['image_path']) ? Storage::url($item['image_path']) : '',
                    'image_alt' => $item['image_alt'] ?? '',
                    'target_url' => $item['target_url'] ?? '',
                ];
            }

            return $data;
        });
    }

    /**
     * 清除前台快取
     */
    public function clearFrontendCache(): void
    {
        Cache::forget('frontend:hero_slides');
    }

    /**
     * 正規化儲存資料
     */
    protected function normalizePayload(array $data, bool $isUpdate): array
    {
        foreach (['image_alt', 'eyebrow', 'title', 'description'] as $field) {
            $data[$field] = isset($data[$field])
                ? trim(strip_tags((string) $data[$field]))
                : null;
        }

        $data['target_url'] = isset($data['target_url']) ? trim((string) $data['target_url']) : null;

        $data['image_alt'] = $data['image_alt'] === '' ? null : $data['image_alt'];
        $data['eyebrow'] = $data['eyebrow'] === '' ? null : $data['eyebrow'];
        $data['description'] = $data['description'] === '' ? null : $data['description'];
        $data['target_url'] = $data['target_url'] === '' ? null : $data['target_url'];
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = (int) ($data['is_active'] ?? STATUS_ACTIVE);
        $data['start_at'] = Carbon::createFromFormat('Y-m-d\TH:i', (string) $data['start_at'])->format('Y-m-d H:i:s');
        $data['end_at'] = !empty($data['end_at'])
            ? Carbon::createFromFormat('Y-m-d\TH:i', (string) $data['end_at'])->format('Y-m-d H:i:s')
            : null;

        $employee = session(ADMIN_AUTH_SESSION);
        if (!empty($employee['id'])) {
            if (!$isUpdate) {
                $data['created_by'] = $employee['id'];
            }
            $data['updated_by'] = $employee['id'];
        }

        unset($data['id']);

        return $data;
    }

    /**
     * 格式化輪播資料
     */
    protected function formatData(array $data, bool $includeDetail = false): array
    {
        $data['is_active_display'] = config('constants.status')[$data['is_active'] ?? STATUS_ACTIVE]
            ?? config('constants.status.' . STATUS_INACTIVE);
        $data['status_badge_class'] = ($data['is_active'] ?? STATUS_INACTIVE) === STATUS_ACTIVE
            ? 'bg-emerald-50 text-emerald-600'
            : 'bg-slate-100 text-slate-500';
        $data['start_at_display'] = !empty($data['start_at'])
            ? Carbon::parse($data['start_at'])->format('Y-m-d H:i')
            : '';
        $data['end_at_display'] = !empty($data['end_at'])
            ? Carbon::parse($data['end_at'])->format('Y-m-d H:i')
            : '永久';
        $data['start_at_input'] = !empty($data['start_at'])
            ? Carbon::parse($data['start_at'])->format('Y-m-d\TH:i')
            : '';
        $data['end_at_input'] = !empty($data['end_at'])
            ? Carbon::parse($data['end_at'])->format('Y-m-d\TH:i')
            : '';
        $data['image_url'] = !empty($data['image_path']) ? Storage::url($data['image_path']) : '';
        $data['target_url'] = $data['target_url'] ?? '';

        if (!$includeDetail) {
            $data['description_preview'] = Str::limit((string) ($data['description'] ?? ''), 80, '...');
        }

        return $data;
    }
}


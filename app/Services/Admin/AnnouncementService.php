<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AnnouncementRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AnnouncementService
{
    # 建構元
    public function __construct(protected AnnouncementRepository $repository)
    {
    }

    /**
     * 取得分頁公告資料
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 20): array
    {
        $filters = $this->normalizeFilters($filters);
        $paginator = $this->repository->fetchPaginatedData($filters, $perPage);

        $data = [];
        foreach ($paginator->items() as $announcement) {
            $data[] = $this->formatData($announcement->toArray());
        }

        return [
            'data' => $data,
            'pagination' => $paginator,
        ];
    }

    /**
     * 依照 ID 取得公告資料
     * @throws \Exception
     */
    public function fetchDataByID(int $id): array
    {
        $data = $this->repository->fetchDataByID($id);

        if (empty($data)) {
            throw new \Exception('查無此公告資料！');
        }

        return $this->formatData($data, true);
    }

    /**
     * 新增公告
     * @throws \Exception
     */
    public function addData(array $data): int
    {
        $data = $this->normalizePayload($data, false);
        $this->validateSystemConflict($data);

        $announcement = $this->repository->addData($data);

        if (empty($announcement->id)) {
            throw new \Exception('新增公告資料失敗！');
        }

        $this->clearFrontendCache();

        return $announcement->id;
    }

    /**
     * 更新公告
     * @throws \Exception
     */
    public function updateData(int $id, array $data): int
    {
        $data = $this->normalizePayload($data, true);
        $this->validateSystemConflict($data, $id);
        $this->repository->updateData($id, $data);
        $this->clearFrontendCache();

        return $id;
    }

    /**
     * 刪除公告
     */
    public function deleteData(int $id): bool
    {
        $result = $this->repository->deleteData($id);
        $this->clearFrontendCache();

        return $result;
    }

    /**
     * 清除前台快取
     */
    public function clearFrontendCache(): void
    {
        Cache::forget('frontend:announcement:system');
        Cache::forget('frontend:announcement:home_latest');
    }

    /**
     * 正規化篩選條件
     */
    protected function normalizeFilters(array $filters): array
    {
        foreach (['keyword', 'start_from', 'start_to'] as $key) {
            if (isset($filters[$key])) {
                $filters[$key] = trim((string) $filters[$key]);
            }
        }

        return $filters;
    }

    /**
     * 正規化儲存資料
     */
    protected function normalizePayload(array $data, bool $isUpdate): array
    {
        $data['type'] = (int) ($data['type'] ?? ANNOUNCEMENT_TYPE_GENERAL);
        $data['title'] = trim(strip_tags((string) ($data['title'] ?? '')));
        $data['summary'] = trim(strip_tags((string) ($data['summary'] ?? '')));
        $data['summary'] = $data['summary'] === '' ? null : $data['summary'];
        $data['content'] = trim(strip_tags((string) ($data['content'] ?? '')));
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
     * 檢查全系統公告衝突
     * @throws \Exception
     */
    protected function validateSystemConflict(array $data, int $ignoreId = 0): void
    {
        if (($data['type'] ?? ANNOUNCEMENT_TYPE_GENERAL) !== ANNOUNCEMENT_TYPE_SYSTEM) {
            return;
        }

        if (($data['is_active'] ?? STATUS_INACTIVE) !== STATUS_ACTIVE) {
            return;
        }

        $conflict = $this->repository->findSystemConflict($data, $ignoreId);

        if (empty($conflict)) {
            return;
        }

        $conflictEndAt = !empty($conflict->end_at)
            ? $conflict->end_at->format('Y-m-d H:i')
            : '永久顯示';

        throw new \Exception('全系統公告時段與「' . $conflict->title . '」重疊（' . $conflict->start_at->format('Y-m-d H:i') . ' ~ ' . $conflictEndAt . '）。');
    }

    /**
     * 格式化顯示資料
     */
    protected function formatData(array $value, bool $includeDetail = false): array
    {
        $value['type_display'] = config('constants.announcement_type')[$value['type'] ?? ANNOUNCEMENT_TYPE_GENERAL]
            ?? config('constants.announcement_type.' . ANNOUNCEMENT_TYPE_GENERAL);
        $value['type_badge_class'] = ($value['type'] ?? ANNOUNCEMENT_TYPE_GENERAL) == ANNOUNCEMENT_TYPE_SYSTEM
            ? 'bg-amber-50 text-amber-700'
            : 'bg-blue-50 text-blue-700';
        $value['is_active_display'] = config('constants.status')[$value['is_active'] ?? STATUS_ACTIVE]
            ?? config('constants.status.' . STATUS_INACTIVE);
        $value['created_at_display'] = !empty($value['created_at'])
            ? Carbon::parse($value['created_at'])->format('Y-m-d H:i')
            : '';
        $value['updated_at_display'] = !empty($value['updated_at'])
            ? Carbon::parse($value['updated_at'])->format('Y-m-d H:i')
            : '';
        $value['start_at_display'] = !empty($value['start_at'])
            ? Carbon::parse($value['start_at'])->format('Y-m-d H:i')
            : '';
        $value['end_at_display'] = !empty($value['end_at'])
            ? Carbon::parse($value['end_at'])->format('Y-m-d H:i')
            : '永久';
        $value['start_at_input'] = !empty($value['start_at'])
            ? Carbon::parse($value['start_at'])->format('Y-m-d\TH:i')
            : '';
        $value['end_at_input'] = !empty($value['end_at'])
            ? Carbon::parse($value['end_at'])->format('Y-m-d\TH:i')
            : '';
        $value['creator_name'] = $value['creator']['name'] ?? '';
        $value['updater_name'] = $value['updater']['name'] ?? '';

        if (!$includeDetail) {
            $value['content_preview'] = mb_strimwidth((string) ($value['content'] ?? ''), 0, 120, '...');
        }

        return $value;
    }
}



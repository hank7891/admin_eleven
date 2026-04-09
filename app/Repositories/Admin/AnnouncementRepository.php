<?php

namespace App\Repositories\Admin;

use App\Models\Announcement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementRepository
{
    # 建構元
    public function __construct(protected Announcement $model)
    {
    }

    /**
     * 取得分頁資料（含篩選）
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model::query()->with(['creator', 'updater']);

        # 關鍵字搜尋
        if (!empty($filters['keyword'])) {
            $query->where(function (Builder $builder) use ($filters) {
                $builder->where('title', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('summary', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('content', 'like', '%' . $filters['keyword'] . '%');
            });
        }

        # 類型篩選
        if (isset($filters['type']) && $filters['type'] !== '') {
            $query->where('type', $filters['type']);
        }

        # 狀態篩選
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        # 開始時間區間篩選
        if (!empty($filters['start_from'])) {
            $query->where('start_at', '>=', $filters['start_from'] . ' 00:00:00');
        }

        if (!empty($filters['start_to'])) {
            $query->where('start_at', '<=', $filters['start_to'] . ' 23:59:59');
        }

        return $query->orderByDesc('start_at')->orderByDesc('id')->paginate($perPage);
    }

    /**
     * 依照 ID 取得資料
     */
    public function fetchDataByID(int $id): array
    {
        $announcement = $this->model::with(['creator', 'updater'])->find($id);

        return !empty($announcement) ? $announcement->toArray() : [];
    }

    /**
     * 新增資料
     */
    public function addData(array $data): object
    {
        return $this->model::create($data);
    }

    /**
     * 修改資料
     * @throws \Exception
     */
    public function updateData(int $id, array $data): object
    {
        $announcement = $this->model::find($id);

        if (empty($announcement)) {
            throw new \Exception('修改資料取得錯誤！');
        }

        $announcement->update($data);

        return $announcement;
    }

    /**
     * 刪除資料
     * @throws \Exception
     */
    public function deleteData(int $id): bool
    {
        $announcement = $this->model::find($id);

        if (empty($announcement)) {
            throw new \Exception('刪除資料取得錯誤！');
        }

        return $announcement->delete();
    }

    /**
     * 取得衝突的全系統公告
     */
    public function findSystemConflict(array $data, int $ignoreId = 0): ?Announcement
    {
        $startAt = $data['start_at'];
        $endAt = $data['end_at'] ?? null;

        $query = $this->model::query()
            ->system()
            ->active();

        if ($ignoreId > 0) {
            $query->where('id', '!=', $ignoreId);
        }

        $query->where(function (Builder $builder) use ($startAt, $endAt) {
            if (empty($endAt)) {
                $builder->whereNull('end_at')
                    ->orWhere('end_at', '>', $startAt);

                return;
            }

            $builder->where(function (Builder $innerBuilder) use ($startAt, $endAt) {
                $innerBuilder->whereNull('end_at')
                    ->where('start_at', '<', $endAt);
            })->orWhere(function (Builder $innerBuilder) use ($startAt, $endAt) {
                $innerBuilder->whereNotNull('end_at')
                    ->where('start_at', '<', $endAt)
                    ->where('end_at', '>', $startAt);
            });
        });

        return $query->orderBy('start_at')->first();
    }

    /**
     * 取得目前生效的全系統公告
     */
    public function fetchCurrentSystemAnnouncement(): ?Announcement
    {
        return $this->model::query()
            ->system()
            ->active()
            ->inEffect()
            ->orderByDesc('start_at')
            ->first();
    }

    /**
     * 取得最新生效的一般公告
     */
    public function fetchLatestGeneralAnnouncements(int $limit = 5)
    {
        return $this->model::query()
            ->general()
            ->active()
            ->inEffect()
            ->orderByDesc('start_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /**
     * 取得前台公告分頁資料
     */
    public function fetchFrontendPaginatedData(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model::query()
            ->general()
            ->active()
            ->inEffect();

        # 關鍵字搜尋
        if (!empty($filters['keyword'])) {
            $query->where(function (Builder $builder) use ($filters) {
                $builder->where('title', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('summary', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('content', 'like', '%' . $filters['keyword'] . '%');
            });
        }

        # 起迄日期篩選
        if (!empty($filters['date_from'])) {
            $query->where('start_at', '>=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $query->where('start_at', '<=', $filters['date_to'] . ' 23:59:59');
        }

        return $query->orderByDesc('start_at')->orderByDesc('id')->paginate($perPage);
    }

    /**
     * 取得前台可閱讀的公告
     */
    public function fetchFrontendDataByID(int $id): ?Announcement
    {
        return $this->model::query()
            ->general()
            ->active()
            ->inEffect()
            ->find($id);
    }

    /**
     * 取得更多文章
     */
    public function fetchMoreFrontendAnnouncements(int $excludeId, int $limit = 3)
    {
        return $this->model::query()
            ->general()
            ->active()
            ->inEffect()
            ->where('id', '!=', $excludeId)
            ->orderByDesc('start_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}


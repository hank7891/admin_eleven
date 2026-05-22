<?php

namespace App\Repositories\Admin;

use App\Models\AdminLog;
use Illuminate\Database\Eloquent\Collection;

class AdminLogRepository
{
    # 建構元
    public function __construct(protected AdminLog $model)
    {
    }

    /**
     * 取得最近 N 筆操作日誌（Dashboard 近期操作用，依操作時間倒序）
     */
    public function fetchRecent(int $limit = 5): Collection
    {
        return $this->model::query()
            ->orderByDesc('operated_at')
            ->limit($limit)
            ->get();
    }
}

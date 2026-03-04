<?php

namespace App\Repositories\Admin;

use App\Models\AdminLog;
use Carbon\Carbon;

class AdminLogRepository
{
    # 建構元
    public function __construct(protected AdminLog $model)
    {
    }

    /**
     * 取得所有日誌
     * @return array
     */
    public function fetchAllData(): array
    {
        return $this->model::all()->toArray();
    }

    /**
     * 依照 id 取得日誌
     * @param int $id
     * @return array
     */
    public function fetchDataByID(int $id): array
    {
        $log = $this->model::find($id);
        return !empty($log) ? $log->toArray() : [];
    }

    /**
     * 依照條件取得日誌列表
     * @param array $conditions
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function fetchByConditions(array $conditions = [], int $perPage = 15)
    {
        $query = $this->model::query();

        if (!empty($conditions['module'])) {
            $query->where('module', $conditions['module']);
        }

        if (!empty($conditions['action'])) {
            $query->where('action', $conditions['action']);
        }

        if (!empty($conditions['employee_id'])) {
            $query->where('employee_id', $conditions['employee_id']);
        }

        if (!empty($conditions['date_from'])) {
            $query->where('operated_at', '>=', $conditions['date_from']);
        }

        if (!empty($conditions['date_to'])) {
            $query->where('operated_at', '<=', $conditions['date_to']);
        }

        return $query->orderBy('operated_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * 新增日誌
     * @param array $data
     * @return AdminLog
     */
    public function addData(array $data): AdminLog
    {
        return $this->model::create($data);
    }

    /**
     * 刪除過期日誌
     * @param int $daysToKeep
     * @return int
     */
    public function deleteExpiredLogs(int $daysToKeep = 90): int
    {
        $cutoffDate = Carbon::now()->subDays($daysToKeep);
        return $this->model::where('operated_at', '<', $cutoffDate)->delete();
    }
}


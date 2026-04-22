<?php

namespace App\Repositories\Admin;

use App\Models\MemberOperationLog;

class MemberOperationLogRepository
{
    # 建構元
    public function __construct(protected MemberOperationLog $memberOperationLog)
    {
    }

    /**
     * 取得會員操作日誌列表（分頁）
     * @param int $perPage
     * @param string|null $memberKeyword 操作者姓名或目標名稱模糊搜尋
     * @param string|null $ipAddress IP 位址模糊搜尋
     * @param string|null $module 模組
     * @param string|null $action 新增 / 編輯 / 刪除
     * @param string|null $dateFrom
     * @param string|null $dateTo
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function fetchList(
        int $perPage = 20,
        ?string $memberKeyword = null,
        ?string $ipAddress = null,
        ?string $module = null,
        ?string $action = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ) {
        $query = $this->memberOperationLog::query();

        # 操作者姓名或目標名稱模糊搜尋
        if (!empty($memberKeyword)) {
            $query->where(function ($q) use ($memberKeyword) {
                $q->where('operator_name', 'LIKE', '%' . $memberKeyword . '%')
                  ->orWhere('target_name', 'LIKE', '%' . $memberKeyword . '%');
            });
        }

        if (!empty($ipAddress)) {
            $query->where('ip_address', 'LIKE', '%' . $ipAddress . '%');
        }

        if (!empty($module)) {
            $query->where('module', $module);
        }

        if (!empty($action)) {
            $query->where('action', $action);
        }

        if (!empty($dateFrom)) {
            $query->where('operated_at', '>=', $dateFrom . ' 00:00:00');
        }

        if (!empty($dateTo)) {
            $query->where('operated_at', '<=', $dateTo . ' 23:59:59');
        }

        return $query->orderBy('operated_at', 'desc')->paginate($perPage);
    }

    /**
     * 依照 ID 取得單筆日誌
     * @param int $id
     *
     * @return MemberOperationLog
     * @throws \Exception
     */
    public function fetchDataByID(int $id): MemberOperationLog
    {
        $log = $this->memberOperationLog::find($id);

        if (empty($log)) {
            throw new \Exception('會員操作日誌資料不存在！');
        }

        return $log;
    }
}

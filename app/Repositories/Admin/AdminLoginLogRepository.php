<?php

namespace App\Repositories\Admin;

use App\Models\AdminLoginLog;

class AdminLoginLogRepository
{
    # 建構元
    public function __construct(protected AdminLoginLog $adminLoginLog)
    {

    }

    /**
     * 新增日誌
     * @param array $data
     *
     * @return object
     */
    public function addData(array $data): object
    {
        return $this->adminLoginLog::create($data);
    }

    /**
     * 取得日誌列表（分頁）
     * @param int $perPage
     * @param string|null $operatorKeyword 操作者名稱或帳號模糊搜尋
     * @param string|null $ipAddress IP 位址模糊搜尋
     * @param string|null $action
     * @param int|null $status
     * @param string|null $dateFrom
     * @param string|null $dateTo
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function fetchList(
        int $perPage = 20,
        ?string $operatorKeyword = null,
        ?string $ipAddress = null,
        ?string $action = null,
        ?int $status = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ) {
        $query = $this->adminLoginLog::query();

        # 操作者名稱或帳號模糊搜尋
        if (!empty($operatorKeyword)) {
            $query->where(function ($q) use ($operatorKeyword) {
                $q->where('employee_name', 'LIKE', '%' . $operatorKeyword . '%')
                  ->orWhere('account', 'LIKE', '%' . $operatorKeyword . '%');
            });
        }

        # IP 位址模糊搜尋
        if (!empty($ipAddress)) {
            $query->where('ip_address', 'LIKE', '%' . $ipAddress . '%');
        }

        if (!is_null($action) && $action !== '') {
            $query->where('action', $action);
        }

        if (!is_null($status) && $status !== '') {
            $query->where('status', $status);
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
     * @return AdminLoginLog
     * @throws \Exception
     */
    public function fetchDataByID(int $id): AdminLoginLog
    {
        $log = $this->adminLoginLog::find($id);

        if (empty($log)) {
            throw new \Exception('登入日誌資料不存在！');
        }

        return $log;
    }
}

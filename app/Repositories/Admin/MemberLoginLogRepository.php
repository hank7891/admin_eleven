<?php

namespace App\Repositories\Admin;

use App\Models\MemberLoginLog;

class MemberLoginLogRepository
{
    # 建構元
    public function __construct(protected MemberLoginLog $memberLoginLog)
    {
    }

    /**
     * 取得會員登入日誌列表（分頁）
     * @param int $perPage
     * @param string|null $memberKeyword 帳號或姓名模糊搜尋
     * @param string|null $ipAddress IP 位址模糊搜尋
     * @param string|null $action 登入 / 登出 / 註冊
     * @param int|null $status 成功 / 失敗
     * @param string|null $dateFrom
     * @param string|null $dateTo
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function fetchList(
        int $perPage = 20,
        ?string $memberKeyword = null,
        ?string $ipAddress = null,
        ?string $action = null,
        ?int $status = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ) {
        $query = $this->memberLoginLog::query();

        # 帳號或姓名模糊搜尋
        if (!empty($memberKeyword)) {
            $query->where(function ($q) use ($memberKeyword) {
                $q->where('account', 'LIKE', '%' . $memberKeyword . '%')
                  ->orWhere('member_name', 'LIKE', '%' . $memberKeyword . '%');
            });
        }

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
     * @return MemberLoginLog
     * @throws \Exception
     */
    public function fetchDataByID(int $id): MemberLoginLog
    {
        $log = $this->memberLoginLog::find($id);

        if (empty($log)) {
            throw new \Exception('會員登入日誌資料不存在！');
        }

        return $log;
    }
}

<?php

namespace App\Services\Admin;

use App\Models\MemberLoginLog;
use App\Repositories\Admin\MemberLoginLogRepository;

class MemberLoginLogService
{
    # 建構元
    public function __construct(protected MemberLoginLogRepository $memberLoginLogRepository)
    {
    }

    /**
     * 取得會員登入日誌列表（分頁）
     * @param int $perPage
     * @param string|null $memberKeyword 帳號或姓名模糊搜尋
     * @param string|null $ipAddress
     * @param string|null $action
     * @param int|null $status
     * @param string|null $dateFrom
     * @param string|null $dateTo
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getLogList(
        int $perPage = 20,
        ?string $memberKeyword = null,
        ?string $ipAddress = null,
        ?string $action = null,
        ?int $status = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ) {
        return $this->memberLoginLogRepository->fetchList(
            $perPage,
            $memberKeyword,
            $ipAddress,
            $action,
            $status,
            $dateFrom,
            $dateTo
        );
    }

    /**
     * 取得單筆會員登入日誌詳情
     * @param int $id
     *
     * @return MemberLoginLog
     * @throws \Exception
     */
    public function getLogDetail(int $id): MemberLoginLog
    {
        return $this->memberLoginLogRepository->fetchDataByID($id);
    }
}

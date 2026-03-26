<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdminLoginLogRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminLoginLogService
{
    # 建構元
    public function __construct(protected AdminLoginLogRepository $adminLoginLogRepository)
    {

    }

    /**
     * 記錄登入成功
     * @param Request $request
     * @param int $employeeId
     * @param string $account
     * @param string $employeeName
     *
     * @return object
     */
    public function recordLoginSuccess(Request $request, int $employeeId, string $account, string $employeeName): object
    {
        return $this->adminLoginLogRepository->addData([
            'employee_id'   => $employeeId,
            'account'       => $account,
            'employee_name' => $employeeName,
            'action'        => LOGIN_LOG_ACTION_LOGIN,
            'status'        => LOGIN_LOG_STATUS_SUCCESS,
            'ip_address'    => $this->getClientIp($request),
            'operated_at'   => Carbon::now(),
        ]);
    }

    /**
     * 記錄登入失敗
     * @param Request $request
     * @param string $account
     * @param string $failReason
     *
     * @return object
     */
    public function recordLoginFail(Request $request, string $account, string $failReason): object
    {
        return $this->adminLoginLogRepository->addData([
            'employee_id'   => null,
            'account'       => $account,
            'employee_name' => null,
            'action'        => LOGIN_LOG_ACTION_LOGIN,
            'status'        => LOGIN_LOG_STATUS_FAIL,
            'fail_reason'   => $failReason,
            'ip_address'    => $this->getClientIp($request),
            'operated_at'   => Carbon::now(),
        ]);
    }

    /**
     * 記錄登出
     * @param Request $request
     * @param int $employeeId
     * @param string $account
     * @param string $employeeName
     *
     * @return object
     */
    public function recordLogout(Request $request, int $employeeId, string $account, string $employeeName): object
    {
        return $this->adminLoginLogRepository->addData([
            'employee_id'   => $employeeId,
            'account'       => $account,
            'employee_name' => $employeeName,
            'action'        => LOGIN_LOG_ACTION_LOGOUT,
            'status'        => LOGIN_LOG_STATUS_SUCCESS,
            'ip_address'    => $this->getClientIp($request),
            'operated_at'   => Carbon::now(),
        ]);
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
    public function getLogList(
        int $perPage = 20,
        ?string $operatorKeyword = null,
        ?string $ipAddress = null,
        ?string $action = null,
        ?int $status = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ) {
        return $this->adminLoginLogRepository->fetchList($perPage, $operatorKeyword, $ipAddress, $action, $status, $dateFrom, $dateTo);
    }

    /**
     * 取得單筆日誌
     * @param int $id
     *
     * @return \App\Models\AdminLoginLog
     */
    public function getLogDetail(int $id): \App\Models\AdminLoginLog
    {
        return $this->adminLoginLogRepository->fetchDataByID($id);
    }

    /**
     * 取得客戶端 IP
     * @param Request $request
     *
     * @return string
     */
    protected function getClientIp(Request $request): string
    {
        $forwarded = $request->header('X-Forwarded-For');
        if (!empty($forwarded)) {
            $ips = explode(',', $forwarded);
            return trim($ips[0]);
        }

        $realIp = $request->header('X-Real-IP');
        if (!empty($realIp)) {
            return $realIp;
        }

        return $request->ip() ?? '0.0.0.0';
    }
}

<?php

namespace App\Services\Frontend;

use App\Models\MemberLoginLog;
use App\Repositories\Frontend\MemberLoginLogRepository;
use Illuminate\Http\Request;

class MemberLoginLogService
{
    # 建構元
    public function __construct(protected MemberLoginLogRepository $memberLoginLogRepository)
    {
    }

    /**
     * 記錄登入成功
     */
    public function recordLoginSuccess(Request $request, int $memberId, string $account, string $memberName): MemberLoginLog
    {
        return $this->memberLoginLogRepository->addData([
            'member_id' => $memberId,
            'account' => $account,
            'member_name' => $memberName,
            'action' => MEMBER_LOGIN_LOG_ACTION_LOGIN,
            'status' => MEMBER_LOGIN_LOG_STATUS_SUCCESS,
            'ip_address' => $this->getClientIp($request),
            'user_agent' => $request->userAgent(),
            'operated_at' => now(),
        ]);
    }

    /**
     * 記錄登入失敗
     */
    public function recordLoginFail(Request $request, string $account, string $failReason): MemberLoginLog
    {
        return $this->memberLoginLogRepository->addData([
            'member_id' => null,
            'account' => $account,
            'member_name' => null,
            'action' => MEMBER_LOGIN_LOG_ACTION_LOGIN,
            'status' => MEMBER_LOGIN_LOG_STATUS_FAIL,
            'fail_reason' => $failReason,
            'ip_address' => $this->getClientIp($request),
            'user_agent' => $request->userAgent(),
            'operated_at' => now(),
        ]);
    }

    /**
     * 記錄登出
     */
    public function recordLogout(Request $request, int $memberId, string $account, string $memberName): MemberLoginLog
    {
        return $this->memberLoginLogRepository->addData([
            'member_id' => $memberId,
            'account' => $account,
            'member_name' => $memberName,
            'action' => MEMBER_LOGIN_LOG_ACTION_LOGOUT,
            'status' => MEMBER_LOGIN_LOG_STATUS_SUCCESS,
            'ip_address' => $this->getClientIp($request),
            'user_agent' => $request->userAgent(),
            'operated_at' => now(),
        ]);
    }

    /**
     * 記錄註冊成功
     */
    public function recordRegisterSuccess(Request $request, int $memberId, string $account, string $memberName): MemberLoginLog
    {
        return $this->memberLoginLogRepository->addData([
            'member_id' => $memberId,
            'account' => $account,
            'member_name' => $memberName,
            'action' => MEMBER_LOGIN_LOG_ACTION_REGISTER,
            'status' => MEMBER_LOGIN_LOG_STATUS_SUCCESS,
            'ip_address' => $this->getClientIp($request),
            'user_agent' => $request->userAgent(),
            'operated_at' => now(),
        ]);
    }

    /**
     * 取得客戶端 IP
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

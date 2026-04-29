<?php

namespace App\Repositories\Frontend;

use App\Models\MemberLoginLog;
use Illuminate\Support\Carbon;

class MemberLoginLogRepository
{
    # 建構元
    public function __construct(protected MemberLoginLog $memberLoginLog)
    {
    }

    /**
     * 新增登入日誌
     */
    public function addData(array $data): MemberLoginLog
    {
        return $this->memberLoginLog::create($data);
    }

    /**
     * 取得會員最新一次成功登入時間
     */
    public function getLastLoginAtByMemberId(int $memberId): ?Carbon
    {
        # 條件：當前會員 + LOGIN action + SUCCESS status；排序 operated_at DESC 取第一筆
        $log = $this->memberLoginLog::query()
            ->where('member_id', $memberId)
            ->where('action', MEMBER_LOGIN_LOG_ACTION_LOGIN)
            ->where('status', MEMBER_LOGIN_LOG_STATUS_SUCCESS)
            ->orderByDesc('operated_at')
            ->first();

        return $log?->operated_at;
    }
}

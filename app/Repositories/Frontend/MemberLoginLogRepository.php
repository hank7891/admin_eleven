<?php

namespace App\Repositories\Frontend;

use App\Models\MemberLoginLog;

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
}


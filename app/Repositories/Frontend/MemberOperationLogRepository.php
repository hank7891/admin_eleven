<?php

namespace App\Repositories\Frontend;

use App\Models\MemberOperationLog;

class MemberOperationLogRepository
{
    # 建構元
    public function __construct(protected MemberOperationLog $memberOperationLog)
    {
    }

    /**
     * 新增操作日誌
     */
    public function addData(array $data): MemberOperationLog
    {
        return $this->memberOperationLog::create($data);
    }
}


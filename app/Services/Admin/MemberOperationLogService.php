<?php

namespace App\Services\Admin;

use App\Models\MemberOperationLog;
use App\Repositories\Admin\MemberOperationLogRepository;

class MemberOperationLogService
{
    # 建構元
    public function __construct(protected MemberOperationLogRepository $memberOperationLogRepository)
    {
    }

    /**
     * 取得會員操作日誌列表（分頁）
     * @param int $perPage
     * @param string|null $memberKeyword 操作者姓名或目標名稱模糊搜尋
     * @param string|null $ipAddress
     * @param string|null $module
     * @param string|null $action
     * @param string|null $dateFrom
     * @param string|null $dateTo
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getLogList(
        int $perPage = 20,
        ?string $memberKeyword = null,
        ?string $ipAddress = null,
        ?string $module = null,
        ?string $action = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ) {
        return $this->memberOperationLogRepository->fetchList(
            $perPage,
            $memberKeyword,
            $ipAddress,
            $module,
            $action,
            $dateFrom,
            $dateTo
        );
    }

    /**
     * 取得單筆會員操作日誌詳情
     * 讀取層再次過濾敏感欄位，作為資安二道防線
     * @param int $id
     *
     * @return MemberOperationLog
     * @throws \Exception
     */
    public function getLogDetail(int $id): MemberOperationLog
    {
        $log = $this->memberOperationLogRepository->fetchDataByID($id);

        # 即使寫入層已過濾，讀取層再次剔除敏感欄位
        $log->changes = $this->filterSensitiveFields($log->changes ?? []);

        return $log;
    }

    /**
     * 依 config 設定過濾敏感欄位
     * @param array|null $changes
     *
     * @return array
     */
    protected function filterSensitiveFields(?array $changes): array
    {
        if (empty($changes)) {
            return [];
        }

        $sensitiveFields = config('member_log.sensitive_fields', []);

        foreach ($sensitiveFields as $field) {
            if (array_key_exists($field, $changes)) {
                unset($changes[$field]);
            }
        }

        return $changes;
    }
}

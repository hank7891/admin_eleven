<?php

namespace App\Services\Frontend;

use App\Models\MemberOperationLog;
use App\Repositories\Frontend\MemberOperationLogRepository;
use Illuminate\Http\Request;

class MemberLogService
{
    # 建構元
    public function __construct(protected MemberOperationLogRepository $memberOperationLogRepository)
    {
    }

    /**
     * 記錄操作日誌
     */
    public function record(
        Request $request,
        string $module,
        string $action,
        int $targetId,
        ?string $targetName = null,
        array $changes = [],
        ?string $remarks = null
    ): MemberOperationLog {
        $member = session(MEMBER_AUTH_SESSION);

        if (empty($member) || empty($member['id'])) {
            throw new \Exception('操作者訊息未找到！');
        }

        return $this->memberOperationLogRepository->addData([
            'member_id' => (int) $member['id'],
            'operator_name' => (string) ($member['name'] ?? ''),
            'ip_address' => $this->getClientIp($request),
            'module' => $module,
            'action' => $action,
            'target_id' => $targetId,
            'target_name' => $targetName,
            'changes' => $this->formatChanges($changes),
            'remarks' => $remarks,
            'operated_at' => now(),
        ]);
    }

    /**
     * 記錄簡易操作日誌
     */
    public function recordSimple(
        Request $request,
        string $module,
        string $action,
        int $targetId,
        ?string $targetName = null,
        ?string $remarks = null
    ): MemberOperationLog {
        return $this->record(
            $request,
            $module,
            $action,
            $targetId,
            $targetName,
            ['operation' => $action],
            $remarks
        );
    }

    /**
     * 記錄更新差異日誌（無差異不寫入）
     */
    public function recordUpdate(
        Request $request,
        string $module,
        int $targetId,
        ?string $targetName = null,
        array $oldData = [],
        array $newData = [],
        ?array $fieldsToLog = null,
        ?string $remarks = null
    ): ?MemberOperationLog {
        $changes = $this->calculateChanges($oldData, $newData, $fieldsToLog);

        if (empty($changes)) {
            return null;
        }

        return $this->record(
            $request,
            $module,
            'update',
            $targetId,
            $targetName,
            $changes,
            $remarks
        );
    }

    /**
     * 計算資料差異
     */
    protected function calculateChanges(array $oldData, array $newData, ?array $fieldsToLog = null): array
    {
        $changes = [];
        $fields = $fieldsToLog ?? array_keys($newData);

        foreach ($fields as $field) {
            $oldValue = $oldData[$field] ?? null;
            $newValue = $newData[$field] ?? null;

            if ($oldValue !== $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * 過濾敏感欄位
     */
    protected function formatChanges(array $changes): array
    {
        $sensitiveFields = config('member_log.sensitive_fields', []);

        foreach ($sensitiveFields as $field) {
            if (isset($changes[$field])) {
                unset($changes[$field]);
            }
        }

        return $changes;
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

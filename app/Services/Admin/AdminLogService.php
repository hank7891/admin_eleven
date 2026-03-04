<?php

namespace App\Services\Admin;

use App\Models\AdminLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminLogService
{
    /**
     * 記錄操作日誌
     *
     * @param Request $request
     * @param string $module 模組名稱 (如: employee, acl_role)
     * @param string $action 操作行為 (create, update, delete)
     * @param int $targetId 被操作的資源ID
     * @param string|null $targetName 被操作資源名稱
     * @param array $changes 修改內容（應包含前後值對比）
     * @param string|null $remarks 操作備註
     * @return AdminLog
     */
    public function record(
        Request $request,
        string $module,
        string $action,
        int $targetId,
        ?string $targetName = null,
        array $changes = [],
        ?string $remarks = null
    ): AdminLog {
        // 取得當前登入的操作者
        $employee = session(ADMIN_AUTH_SESSION);

        if (empty($employee)) {
            throw new \Exception('操作者訊息未找到！');
        }

        // 整理修改內容
        $changesData = $this->formatChanges($changes);

        // 創建日誌記錄
        $log = AdminLog::create([
            'employee_id' => $employee['id'],
            'operator_name' => $employee['name'],
            'ip_address' => $this->getClientIp($request),
            'module' => $module,
            'action' => $action,
            'target_id' => $targetId,
            'target_name' => $targetName,
            'changes' => $changesData,
            'remarks' => $remarks,
            'operated_at' => Carbon::now(),
        ]);

        return $log;
    }

    /**
     * 自動記錄日誌（不需要手動傳遞 changes）
     * 用於簡單的新增/刪除操作
     *
     * @param Request $request
     * @param string $module
     * @param string $action
     * @param int $targetId
     * @param string|null $targetName
     * @param string|null $remarks
     * @return AdminLog
     */
    public function recordSimple(
        Request $request,
        string $module,
        string $action,
        int $targetId,
        ?string $targetName = null,
        ?string $remarks = null
    ): AdminLog {
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
     * 記錄更新操作的前後對比
     *
     * @param Request $request
     * @param string $module
     * @param int $targetId
     * @param string|null $targetName
     * @param array $oldData 修改前的資料
     * @param array $newData 修改後的資料
     * @param array $fieldsToLog 需要記錄的欄位（null 表示全部）
     * @param string|null $remarks
     * @return AdminLog
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
    ): AdminLog {
        // 計算變化內容
        $changes = $this->calculateChanges($oldData, $newData, $fieldsToLog);

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
     * 計算前後變化
     *
     * @param array $oldData
     * @param array $newData
     * @param array|null $fieldsToLog
     * @return array
     */
    protected function calculateChanges(array $oldData, array $newData, ?array $fieldsToLog = null): array
    {
        $changes = [];

        // 如果指定了要記錄的欄位，只記錄這些欄位
        $fields = $fieldsToLog ?? array_keys($newData);

        foreach ($fields as $field) {
            $oldValue = $oldData[$field] ?? null;
            $newValue = $newData[$field] ?? null;

            // 只記錄有變化的欄位
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
     * 格式化修改內容
     * 如果是 JSON 字符串，則解析；如果是陣列，直接使用
     *
     * @param array $changes
     * @return array
     */
    protected function formatChanges(array $changes): array
    {
        // 過濾掉密碼等敏感字段（可選）
        $sensitiveFields = ['password', 'token', 'secret'];

        foreach ($sensitiveFields as $field) {
            if (isset($changes[$field])) {
                unset($changes[$field]);
            }
        }

        return $changes;
    }

    /**
     * 獲取客戶端 IP 地址
     *
     * @param Request $request
     * @return string
     */
    protected function getClientIp(Request $request): string
    {
        // 優先級：X-Forwarded-For > X-Real-IP > REMOTE_ADDR
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        } else {
            return $request->ip() ?? '0.0.0.0';
        }
    }

    /**
     * 取得日誌列表
     *
     * @param int $perPage
     * @param string|null $module
     * @param string|null $action
     * @return \Illuminate\Pagination\Paginator
     */
    public function getLogList(int $perPage = 15, ?string $module = null, ?string $action = null)
    {
        $query = AdminLog::query();

        if (!empty($module)) {
            $query->where('module', $module);
        }

        if (!empty($action)) {
            $query->where('action', $action);
        }

        return $query->orderBy('operated_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * 取得單筆日誌詳情
     *
     * @param int $id
     * @return AdminLog
     * @throws \Exception
     */
    public function getLogDetail(int $id): AdminLog
    {
        $log = AdminLog::findOrFail($id);
        return $log;
    }

    /**
     * 刪除過期日誌（保留 N 天內的記錄）
     *
     * @param int $daysToKeep 保留天數（默認保留 90 天）
     * @return int 刪除的記錄數
     */
    public function deleteExpiredLogs(int $daysToKeep = 90): int
    {
        $cutoffDate = Carbon::now()->subDays($daysToKeep);

        return AdminLog::where('operated_at', '<', $cutoffDate)->delete();
    }
}


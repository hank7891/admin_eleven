<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminLogService;
use App\Services\Share\MessageService;
use Carbon\Carbon;

class AdminLogController extends Controller
{
    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(protected AdminLogService $service)
    {
        $this->settingService = app('setting');
    }

    /**
     * 日誌列表頁面
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function list(Request $request)
    {
        try {
            # 取得篩選條件
            $module = $request->get('module');
            $action = $request->get('action');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            # 構建查詢條件
            $conditions = [];
            if (!empty($module)) {
                $conditions['module'] = $module;
            }
            if (!empty($action)) {
                $conditions['action'] = $action;
            }
            if (!empty($dateFrom)) {
                $conditions['date_from'] = $dateFrom . ' 00:00:00';
            }
            if (!empty($dateTo)) {
                $conditions['date_to'] = $dateTo . ' 23:59:59';
            }

            # 取得日誌列表
            $logs = $this->service->getLogList(
                perPage: 20,
                module: $module,
                action: $action
            );

            # 如果有日期篩選，需要手動過濾
            if (!empty($dateFrom) || !empty($dateTo)) {
                $items = $logs->getCollection()->filter(function ($log) use ($dateFrom, $dateTo) {
                    $operatedAt = Carbon::parse($log->operated_at);

                    if (!empty($dateFrom) && $operatedAt->format('Y-m-d') < $dateFrom) {
                        return false;
                    }
                    if (!empty($dateTo) && $operatedAt->format('Y-m-d') > $dateTo) {
                        return false;
                    }
                    return true;
                });

                $logs->setCollection($items);
            }

            # 格式化日誌數據
            $formattedLogs = $logs->getCollection()->map(function ($log) {
                return [
                    'id' => $log->id,
                    'operator_name' => $log->operator_name,
                    'module' => $log->module,
                    'action' => $log->action,
                    'action_display' => $this->getActionDisplay($log->action),
                    'module_display' => $this->getModuleDisplay($log->module),
                    'target_name' => $log->target_name,
                    'ip_address' => $log->ip_address,
                    'operated_at' => Carbon::parse($log->operated_at)->format('Y-m-d H:i:s'),
                    'created_at' => Carbon::parse($log->created_at)->format('Y-m-d'),
                ];
            })->toArray();

            # 列表欄位
            $fields = [
                'ID' => 'id',
                '操作者' => 'operator_name',
                '模組' => 'module_display',
                '操作' => 'action_display',
                '資源' => 'target_name',
                'IP 位址' => 'ip_address',
                '操作時間' => 'operated_at',
            ];

            $this->settingService->setSetData('pageTitle', '操作日誌');
            $this->settingService->setSetData('editUrl', asset('admin/admin.log/detail') . '/');
            $this->settingService->setSetData('fields', $fields);
            $this->settingService->setSetData('data', $formattedLogs);
            $this->settingService->setSetData('pagination', $logs);

            return view('admin-share/page/log-list', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/');
        }
    }

    /**
     * 日誌詳情頁面
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function detail(int $id)
    {
        try {
            $log = $this->service->getLogDetail($id);

            if (empty($log)) {
                throw new \Exception('查無此操作日誌！');
            }

            # 格式化數據
            $data = [
                'id' => $log->id,
                'operator_name' => $log->operator_name,
                'ip_address' => $log->ip_address,
                'module_display' => $this->getModuleDisplay($log->module),
                'module' => $log->module,
                'action_display' => $this->getActionDisplay($log->action),
                'action' => $log->action,
                'target_id' => $log->target_id,
                'target_name' => $log->target_name,
                'changes' => $log->changes, # JSON 格式保留
                'remarks' => $log->remarks,
                'operated_at' => Carbon::parse($log->operated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
            ];

            $this->settingService->setSetData('data', $data);
            return view('admin/admin-log/detail', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/admin.log/list');
        }
    }

    /**
     * 獲取操作行為的顯示名稱
     */
    private function getActionDisplay(string $action): string
    {
        $actions = [
            'create' => '新增',
            'update' => '編輯',
            'delete' => '刪除',
        ];
        return $actions[$action] ?? $action;
    }

    /**
     * 獲取模組的顯示名稱
     */
    private function getModuleDisplay(string $module): string
    {
        $modules = [
            'employee' => '帳號管理',
            'acl_role' => '角色管理',
            'admin_log' => '操作日誌',
        ];
        return $modules[$module] ?? $module;
    }
}


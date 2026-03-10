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

            # 取得日誌列表（日期篩選在 SQL 層級處理）
            $logs = $this->service->getLogList(
                perPage: 20,
                module: $module,
                action: $action,
                dateFrom: $dateFrom,
                dateTo: $dateTo
            );

            # 格式化日誌數據（使用 Model accessor）
            $formattedLogs = $logs->getCollection()->map(function ($log) {
                return [
                    'id' => $log->id,
                    'operator_name' => $log->operator_name,
                    'module' => $log->module,
                    'action' => $log->action,
                    'action_display' => $log->action_display,
                    'module_display' => $log->module_display,
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
            $this->settingService->setSetData('showAddButton', false);
            $this->settingService->setSetData('actionLabel', '詳情');

            return view('admin-share/page/list', $this->settingService->fetchSetData());
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

            # 格式化數據（使用 Model accessor）
            $data = [
                'id' => $log->id,
                'operator_name' => $log->operator_name,
                'ip_address' => $log->ip_address,
                'module_display' => $log->module_display,
                'module' => $log->module,
                'action_display' => $log->action_display,
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
}

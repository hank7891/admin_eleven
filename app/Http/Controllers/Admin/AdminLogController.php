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
            $filters = [
                'operator_name' => $request->get('operator_name'),
                'ip_address'    => $request->get('ip_address'),
                'module'        => $request->get('module'),
                'date_from'     => $request->get('date_from'),
                'date_to'       => $request->get('date_to'),
            ];

            # 判斷是否有任何篩選條件
            $hasFilter = collect($filters)->filter(fn($v) => !is_null($v) && $v !== '')->isNotEmpty();

            $data = [];
            $pagination = null;

            if ($hasFilter) {
                # 有篩選條件才查詢
                $logs = $this->service->getLogList(
                    perPage: 20,
                    operatorName: $filters['operator_name'],
                    ipAddress: $filters['ip_address'],
                    module: $filters['module'],
                    dateFrom: $filters['date_from'],
                    dateTo: $filters['date_to']
                );

                # 格式化日誌數據
                $data = $logs->getCollection()->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'operator_name' => $log->operator_name,
                        'module_display' => $log->module_display,
                        'action_display' => $log->action_display,
                        'target_name' => $log->target_name,
                        'ip_address' => $log->ip_address,
                        'operated_at' => Carbon::parse($log->operated_at)->format('Y-m-d H:i:s'),
                    ];
                })->toArray();

                $pagination = $logs;
            }

            # 模組選項（下拉選單用）
            $moduleOptions = config('admin_log.modules');

            $this->settingService->setSetData('data', $data);
            $this->settingService->setSetData('pagination', $pagination);
            $this->settingService->setSetData('filters', $filters);
            $this->settingService->setSetData('moduleOptions', $moduleOptions);
            $this->settingService->setSetData('hasFilter', $hasFilter);

            return view('admin/admin-log/list', $this->settingService->fetchSetData());
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

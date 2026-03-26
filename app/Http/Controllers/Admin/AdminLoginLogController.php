<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminLoginLogService;
use App\Services\Share\MessageService;
use Carbon\Carbon;

class AdminLoginLogController extends Controller
{
    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(protected AdminLoginLogService $service)
    {
        $this->settingService = app('setting');
    }

    /**
     * 登入日誌列表
     */
    public function list(Request $request)
    {
        try {
            # 取得篩選條件
            $filters = [
                'operator_keyword' => $request->get('operator_keyword'),
                'ip_address'       => $request->get('ip_address'),
                'action'           => $request->get('action'),
                'status'           => $request->get('status'),
                'date_from'        => $request->get('date_from'),
                'date_to'          => $request->get('date_to'),
            ];

            # 判斷是否有任何篩選條件
            $hasFilter = collect($filters)->filter(fn($v) => !is_null($v) && $v !== '')->isNotEmpty();

            $data = [];
            $pagination = null;

            if ($hasFilter) {
                # 有篩選條件才查詢
                $logs = $this->service->getLogList(
                    perPage: 20,
                    operatorKeyword: $filters['operator_keyword'],
                    ipAddress: $filters['ip_address'],
                    action: $filters['action'],
                    status: !is_null($filters['status']) && $filters['status'] !== '' ? (int) $filters['status'] : null,
                    dateFrom: $filters['date_from'],
                    dateTo: $filters['date_to']
                );

                # 格式化日誌數據
                $data = $logs->getCollection()->map(function ($log) {
                    return [
                        'id'              => $log->id,
                        'account'         => $log->account,
                        'employee_name'   => $log->employee_name ?? '--',
                        'action_display'  => $log->action_display,
                        'status_display'  => $log->status_display,
                        'status'          => $log->status,
                        'ip_address'      => $log->ip_address,
                        'operated_at'     => Carbon::parse($log->operated_at)->format('Y-m-d H:i:s'),
                    ];
                })->toArray();

                $pagination = $logs;
            }

            # 下拉選項
            $actionOptions = config('constants.login_log_action');
            $statusOptions = config('constants.login_log_status');

            $this->settingService->setSetData('data', $data);
            $this->settingService->setSetData('pagination', $pagination);
            $this->settingService->setSetData('filters', $filters);
            $this->settingService->setSetData('actionOptions', $actionOptions);
            $this->settingService->setSetData('statusOptions', $statusOptions);
            $this->settingService->setSetData('hasFilter', $hasFilter);

            return view('admin/admin-login-log/list', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/');
        }
    }

    /**
     * 登入日誌詳情
     * @param int $id
     */
    public function detail(int $id)
    {
        try {
            $log = $this->service->getLogDetail($id);

            $data = [
                'id'             => $log->id,
                'employee_id'    => $log->employee_id,
                'account'        => $log->account,
                'employee_name'  => $log->employee_name ?? '--',
                'action'         => $log->action,
                'action_display' => $log->action_display,
                'status'         => $log->status,
                'status_display' => $log->status_display,
                'fail_reason'    => $log->fail_reason,
                'ip_address'     => $log->ip_address,
                'operated_at'    => Carbon::parse($log->operated_at)->format('Y-m-d H:i:s'),
                'created_at'     => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
            ];

            $this->settingService->setSetData('data', $data);
            return view('admin/admin-login-log/detail', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/admin.login-log/list');
        }
    }
}

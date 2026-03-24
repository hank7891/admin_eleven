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
            $action   = $request->get('action');
            $status   = $request->get('status');
            $dateFrom = $request->get('date_from');
            $dateTo   = $request->get('date_to');

            # 取得日誌列表
            $logs = $this->service->getLogList(
                perPage: 20,
                action: $action,
                status: !is_null($status) && $status !== '' ? (int) $status : null,
                dateFrom: $dateFrom,
                dateTo: $dateTo
            );

            # 格式化日誌數據
            $formattedLogs = $logs->getCollection()->map(function ($log) {
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

            # 列表欄位
            $fields = [
                'ID'       => 'id',
                '帳號'     => 'account',
                '姓名'     => 'employee_name',
                '操作'     => 'action_display',
                '狀態'     => 'status_display',
                'IP 位址'  => 'ip_address',
                '操作時間' => 'operated_at',
            ];

            $this->settingService->setSetData('pageTitle', '登入日誌');
            $this->settingService->setSetData('editUrl', asset('admin/admin.login-log/detail') . '/');
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

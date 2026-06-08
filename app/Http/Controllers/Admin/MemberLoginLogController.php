<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\MemberLoginLogService;
use App\Services\Share\MessageService;
use Carbon\Carbon;

class MemberLoginLogController extends Controller
{
    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(protected MemberLoginLogService $service)
    {
        $this->settingService = app('setting');
    }

    /**
     * 會員登入日誌列表
     */
    public function list(Request $request)
    {
        try {
            # 取得篩選條件
            $filters = [
                'member_keyword' => $request->get('member_keyword'),
                'ip_address'     => $request->get('ip_address'),
                'action'         => $request->get('action'),
                'status'         => $request->get('status'),
                'date_from'      => $request->get('date_from'),
                'date_to'        => $request->get('date_to'),
            ];

            # 判斷是否有任何篩選條件
            $hasFilter = collect($filters)->filter(fn($v) => !is_null($v) && $v !== '')->isNotEmpty();

            $data = [];
            $pagination = null;

            if ($hasFilter) {
                $logs = $this->service->getLogList(
                    perPage: 20,
                    memberKeyword: $filters['member_keyword'],
                    ipAddress: $filters['ip_address'],
                    action: $filters['action'],
                    status: !is_null($filters['status']) && $filters['status'] !== '' ? (int) $filters['status'] : null,
                    dateFrom: $filters['date_from'],
                    dateTo: $filters['date_to']
                );

                $data = $logs->getCollection()->map(function ($log) {
                    return [
                        'id'             => $log->id,
                        'account'        => $log->account,
                        'member_name'    => $log->member_name ?? '--',
                        'action_display' => $log->action_display,
                        'status'         => $log->status,
                        'status_display' => $log->status_display,
                        'status_tone'    => $log->status_tone,
                        'ip_address'     => $log->ip_address,
                        'operated_at'    => Carbon::parse($log->operated_at)->format('Y-m-d H:i:s'),
                    ];
                })->toArray();

                $pagination = $logs;
            }

            $actionOptions = config('constants.member_login_log_action');
            $statusOptions = config('constants.member_login_log_status');

            $this->settingService->setSetData('data', $data);
            $this->settingService->setSetData('pagination', $pagination);
            $this->settingService->setSetData('filters', $filters);
            $this->settingService->setSetData('actionOptions', $actionOptions);
            $this->settingService->setSetData('statusOptions', $statusOptions);
            $this->settingService->setSetData('hasFilter', $hasFilter);

            return view('admin/member-login-log/list', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/');
        }
    }

    /**
     * 會員登入日誌詳情
     * @param int $id
     */
    public function detail(int $id)
    {
        try {
            $log = $this->service->getLogDetail($id);

            $data = [
                'id'             => $log->id,
                'member_id'      => $log->member_id,
                'account'        => $log->account,
                'member_name'    => $log->member_name ?? '--',
                'action'         => $log->action,
                'action_display' => $log->action_display,
                'status'         => $log->status,
                'status_display' => $log->status_display,
                'status_tone'    => $log->status_tone,
                'fail_reason'    => $log->fail_reason,
                'ip_address'     => $log->ip_address,
                'user_agent'     => $log->user_agent,
                'operated_at'    => Carbon::parse($log->operated_at)->format('Y-m-d H:i:s'),
                'created_at'     => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
            ];

            $this->settingService->setSetData('data', $data);
            return view('admin/member-login-log/detail', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/member.login-log/list');
        }
    }
}

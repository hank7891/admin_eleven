<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\MemberOperationLogService;
use App\Services\Share\MessageService;
use Carbon\Carbon;

class MemberOperationLogController extends Controller
{
    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(protected MemberOperationLogService $service)
    {
        $this->settingService = app('setting');
    }

    /**
     * 會員操作日誌列表
     */
    public function list(Request $request)
    {
        try {
            # 取得篩選條件
            $filters = [
                'member_keyword' => $request->get('member_keyword'),
                'ip_address'     => $request->get('ip_address'),
                'module'         => $request->get('module'),
                'action'         => $request->get('action'),
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
                    module: $filters['module'],
                    action: $filters['action'],
                    dateFrom: $filters['date_from'],
                    dateTo: $filters['date_to']
                );

                $data = $logs->getCollection()->map(function ($log) {
                    return [
                        'id'             => $log->id,
                        'operator_name'  => $log->operator_name,
                        'module_display' => $log->module_display,
                        'action_display' => $log->action_display,
                        'target_name'    => $log->target_name,
                        'ip_address'     => $log->ip_address,
                        'operated_at'    => Carbon::parse($log->operated_at)->format('Y-m-d H:i:s'),
                    ];
                })->toArray();

                $pagination = $logs;
            }

            $moduleOptions = config('member_log.modules');
            $actionOptions = config('member_log.actions');

            $this->settingService->setSetData('data', $data);
            $this->settingService->setSetData('pagination', $pagination);
            $this->settingService->setSetData('filters', $filters);
            $this->settingService->setSetData('moduleOptions', $moduleOptions);
            $this->settingService->setSetData('actionOptions', $actionOptions);
            $this->settingService->setSetData('hasFilter', $hasFilter);

            return view('admin/member-operation-log/list', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/');
        }
    }

    /**
     * 會員操作日誌詳情
     * @param int $id
     */
    public function detail(int $id)
    {
        try {
            $log = $this->service->getLogDetail($id);

            $data = [
                'id'             => $log->id,
                'member_id'      => $log->member_id,
                'operator_name'  => $log->operator_name,
                'ip_address'     => $log->ip_address,
                'module'         => $log->module,
                'module_display' => $log->module_display,
                'action'         => $log->action,
                'action_display' => $log->action_display,
                'target_id'      => $log->target_id,
                'target_name'    => $log->target_name,
                'changes'        => $log->changes, # Service 已再次過濾敏感欄位
                'remarks'        => $log->remarks,
                'operated_at'    => Carbon::parse($log->operated_at)->format('Y-m-d H:i:s'),
                'created_at'     => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
            ];

            $this->settingService->setSetData('data', $data);
            return view('admin/member-operation-log/detail', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/member.operation-log/list');
        }
    }
}

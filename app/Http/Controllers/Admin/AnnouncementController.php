<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminLogService;
use App\Services\Admin\AnnouncementService;
use App\Services\Share\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    const POST_SESSION = 'announcement_edit_post';

    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(
        protected AnnouncementService $service,
        protected AdminLogService $logService
    ) {
        $this->settingService = app('setting');
    }

    /**
     * 列表頁面
     */
    public function list(Request $request)
    {
        $filters = $request->only(['keyword', 'type', 'is_active', 'start_from', 'start_to']);
        $result = $this->service->fetchPaginatedData($filters);

        $this->settingService->setSetData('data', $result['data']);
        $this->settingService->setSetData('pagination', $result['pagination']);
        $this->settingService->setSetData('filters', $filters);
        $this->settingService->setSetData('typeOptions', config('constants.announcement_type'));
        $this->settingService->setSetData('statusOptions', config('constants.status'));

        return view('admin/announcement/list', $this->settingService->fetchSetData());
    }

    /**
     * 編輯頁面
     */
    public function edit(int $id)
    {
        try {
            $data = ($id > 0) ? $this->service->fetchDataByID($id) : [];

            if (session(self::POST_SESSION)) {
                $data = session(self::POST_SESSION) + $data;
                session()->forget(self::POST_SESSION);
            }

            $this->settingService->setSetData('data', $data);
            $this->settingService->setSetData('typeOptions', config('constants.announcement_type'));
            $this->settingService->setSetData('statusOptions', config('constants.status'));

            return view('admin/announcement/edit', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/announcement/list');
        }
    }

    /**
     * 編輯實作
     */
    public function editDo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'      => ['required', 'integer', Rule::in(array_keys(config('constants.announcement_type')))],
            'title'     => ['required', 'string', 'max:200'],
            'summary'   => ['nullable', 'string', 'max:500'],
            'content'   => ['required', 'string'],
            'is_active' => ['required', 'integer', Rule::in(array_keys(config('constants.status')))],
            'start_at'  => ['required', 'date_format:Y-m-d\TH:i'],
            'end_at'    => [
                'nullable',
                'required_if:type,' . ANNOUNCEMENT_TYPE_SYSTEM,
                'date_format:Y-m-d\\TH:i',
                'after_or_equal:start_at',
            ],
        ], [
            'type.required'      => '公告類型為必填欄位',
            'title.required'     => '標題為必填欄位',
            'title.max'          => '標題不可超過 200 個字元',
            'summary.max'        => '大綱不可超過 500 個字元',
            'content.required'   => '內文為必填欄位',
            'start_at.required'  => '開始時間為必填欄位',
            'start_at.date_format' => '開始時間格式錯誤',
            'end_at.required_if' => '全系統公告必須設定結束時間',
            'end_at.date_format' => '結束時間格式錯誤',
            'end_at.after_or_equal' => '結束時間不可早於開始時間',
        ]);

        $post = $request->only(['id', 'type', 'title', 'summary', 'content', 'is_active', 'start_at', 'end_at']);

        if ($validator->fails()) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $validator->errors()->first());
            return redirect('admin/announcement/edit/' . ((int) ($post['id'] ?? 0)));
        }

        try {
            $id = (int) ($post['id'] ?? 0);

            if ($id === 0) {
                # 新增
                $id = $this->service->addData($post);

                # 記錄操作日誌
                $this->logService->recordSimple($request, 'announcement', 'create', $id, $post['title'] ?? null);
            } else {
                # 取得修改前資料
                $oldData = $this->service->fetchDataByID($id);

                # 編輯
                $this->service->updateData($id, $post);

                # 記錄操作日誌
                $this->logService->recordUpdate(
                    $request,
                    'announcement',
                    $id,
                    $oldData['title'] ?? null,
                    $oldData,
                    $post,
                    ['type', 'title', 'summary', 'content', 'is_active', 'start_at', 'end_at']
                );
            }

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');
            return redirect('admin/announcement/edit/' . $id);
        } catch (\Exception $e) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/announcement/edit/' . ((int) ($post['id'] ?? 0)));
        }
    }

    /**
     * 刪除
     */
    public function delete(Request $request, int $id)
    {
        try {
            $data = $this->service->fetchDataByID($id);
            $this->service->deleteData($id);

            # 記錄操作日誌
            $this->logService->recordSimple($request, 'announcement', 'delete', $id, $data['title'] ?? null);

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '刪除成功！');
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
        }

        return redirect('admin/announcement/list');
    }
}



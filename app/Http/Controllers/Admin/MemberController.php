<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminLogService;
use App\Services\Admin\MemberService;
use App\Services\Share\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    const POST_SESSION = 'member_edit_post';

    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(
        protected MemberService $service,
        protected AdminLogService $logService
    ) {
        $this->settingService = app('setting');
    }

    /**
     * 列表頁
     */
    public function list(Request $request)
    {
        $filters = $request->only(['keyword', 'status_key', 'date_from', 'date_to']);
        $result = $this->service->fetchPaginatedData($filters);

        $this->settingService->setSetData('data', $result['data']);
        $this->settingService->setSetData('pagination', $result['pagination']);
        $this->settingService->setSetData('filters', $result['filters']);
        $this->settingService->setSetData('statusOptions', config('constants.member_status'));

        return view('Admin.member.list', $this->settingService->fetchSetData());
    }

    /**
     * 編輯頁
     */
    public function edit(int $id)
    {
        try {
            $data = $this->service->getForEdit($id);

            if (session(self::POST_SESSION)) {
                $data = session(self::POST_SESSION) + $data;
                session()->forget(self::POST_SESSION);
            }

            $this->settingService->setSetData('data', $data);
            $this->settingService->setSetData('genderOptions', config('constants.gender'));
            $this->settingService->setSetData('statusOptions', config('constants.member_status'));

            return view('Admin.member.edit', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());

            return redirect('admin/member/list');
        }
    }

    /**
     * 編輯送出
     */
    public function editDo(Request $request)
    {
        $id = (int) $request->input('id', 0);
        $isEdit = $id > 0;

        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-()\s]*$/'],
            'birthday' => ['nullable', 'date'],
            'gender_key' => ['nullable', Rule::in(array_map('strval', array_keys(config('constants.gender'))))],
            'status_key' => ['required', Rule::in(array_keys(config('constants.member_status')))],
            'avatar' => ['nullable', 'file', 'max:' . (config('upload.image.max_size') ?? 5120), 'mimes:' . implode(',', config('upload.image.mimes', []))],
        ];

        if (!$isEdit) {
            $rules['email'] = ['required', 'email', 'max:255', Rule::unique('member', 'email')];
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validator = Validator::make($request->all(), $rules, [
            'email.required' => 'Email 為必填欄位',
            'email.email' => 'Email 格式錯誤',
            'email.unique' => '此 Email 已被使用',
            'name.required' => '姓名為必填欄位',
            'password.required' => '密碼為必填欄位',
            'password.min' => '密碼至少 8 碼',
            'password.confirmed' => '兩次密碼輸入不一致',
            'phone.regex' => '電話僅可輸入數字、+、-、空白、括號',
        ]);

        $post = $request->only([
            'id',
            'email',
            'name',
            'phone',
            'birthday',
            'gender_key',
            'status_key',
            'password',
        ]);

        $sessionPost = $request->only([
            'id',
            'email',
            'name',
            'phone',
            'birthday',
            'gender_key',
            'status_key',
        ]);

        if ($validator->fails()) {
            session([self::POST_SESSION => $sessionPost]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $validator->errors()->first());

            return redirect('admin/member/edit/' . $id);
        }

        try {
            if ($id === 0) {
                $newData = $this->service->addData($post, $request->file('avatar'));

                $this->logService->recordSimple($request, 'member', 'create', (int) $newData['id'], $newData['name'] ?? null);

                MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '新增會員成功！');

                return redirect('admin/member/edit/' . ((int) ($newData['id'] ?? 0)));
            }

            $oldData = $this->service->getForEdit($id);
            $newData = $this->service->updateData($id, $post, $request->file('avatar'));

            $this->logService->recordUpdate(
                $request,
                'member',
                $id,
                $newData['name'] ?? null,
                $oldData,
                $newData,
                ['name', 'phone', 'birthday', 'gender_key', 'status_key', 'avatar_path']
            );

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');

            return redirect('admin/member/edit/' . $id);
        } catch (\Exception $e) {
            session([self::POST_SESSION => $sessionPost]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());

            return redirect('admin/member/edit/' . $id);
        }
    }

    /**
     * 重設密碼
     */
    public function resetPassword(Request $request, int $id)
    {
        try {
            $result = $this->service->resetPassword($id);

            $this->logService->recordSimple(
                $request,
                'member',
                'update',
                $id,
                $result['name'] ?? null,
                '重設會員密碼'
            );

            MessageService::setMessage(
                ADMIN_MESSAGE_SESSION,
                MessageService::SUCCESS,
                '重設密碼成功，新密碼：' . ($result['password'] ?? '') . '（此訊息僅顯示一次）'
            );
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
        }

        return redirect('admin/member/edit/' . $id);
    }
}



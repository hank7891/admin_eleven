<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\Admin\EmployeeService;
use App\Services\Admin\AclRoleService;
use App\Services\Admin\AdminLogService;
use App\Services\Share\FileUploadService;
use App\Services\Share\MessageService;

class EmployeeController extends Controller
{
    const POST_SESSION = 'employee_edit_post';

    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(
        protected EmployeeService $service,
        protected AclRoleService $roleService,
        protected AdminLogService $logService,
        protected FileUploadService $uploadService
    ) {
        $this->settingService = app('setting');
    }

    /**
     * 列表頁面
     */
    public function list(Request $request)
    {
        # 篩選條件
        $filters = $request->only(['account', 'name', 'is_active']);

        # 取得分頁資料
        $result = $this->service->fetchPaginatedData($filters);

        $this->settingService->setSetData('data', $result['data']);
        $this->settingService->setSetData('pagination', $result['pagination']);
        $this->settingService->setSetData('filters', $filters);
        $this->settingService->setSetData('statusOptions', config('constants.status'));

        return view('admin/employee/list', $this->settingService->fetchSetData());
    }

    /**
     * 編輯
     * @param int $id
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(int $id)
    {
        try {
            $data = ($id > 0) ? $this->service->fetchDataByID($id) : [];

            if (session(self::POST_SESSION)) {
                $data = session(self::POST_SESSION) + $data;
                session()->forget(self::POST_SESSION);
            }

            # 取得所有角色（供 checkbox 使用）
            $roles = $this->roleService->fetchAllData();

            $this->settingService->setSetData('data', $data);
            $this->settingService->setSetData('roles', $roles);
            return view('admin/employee/edit', $this->settingService->fetchSetData());
        } catch (\Exception $e) {

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/');
        }
    }

    /**
     * 編輯實作
     * @param Request         $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editDo(Request $request)
    {
        $id = (int) $request->input('id', 0);
        $isEdit = $id > 0;
        $roleIds = $request->input('role_ids', []);

        $rules = [
            'name'                  => ['required', 'string', 'max:50'],
            'password'              => [$isEdit ? 'nullable' : 'required', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => [$isEdit ? 'nullable' : 'required', 'string', 'min:6'],
            'gender'                => ['nullable', 'integer', Rule::in(array_keys(config('constants.gender')))],
            'is_active'             => ['nullable', 'integer', Rule::in(array_keys(config('constants.status')))],
            'birthday'              => ['nullable', 'date'],
            'phone'                 => ['nullable', 'string', 'max:30'],
            'role_ids'              => ['nullable', 'array'],
            'role_ids.*'            => ['integer', 'exists:acl_role,id'],
        ];

        if (!$isEdit) {
            $rules['account'] = ['required', 'string', 'max:50', Rule::unique('employee', 'account')];
        }

        $validator = Validator::make($request->all(), $rules, [
            'account.required'              => '帳號為必填欄位',
            'account.unique'                => '此帳號已被使用',
            'name.required'                 => '姓名為必填欄位',
            'name.max'                      => '姓名不可超過 50 個字元',
            'password.required'             => '密碼為必填欄位',
            'password.min'                  => '密碼至少需要 6 個字元',
            'password.confirmed'            => '兩次密碼輸入不一致',
            'password_confirmation.required'=> '請再次輸入確認密碼',
            'password_confirmation.min'     => '確認密碼至少需要 6 個字元',
            'birthday.date'                 => '生日格式錯誤',
            'phone.max'                     => '電話不可超過 30 個字元',
            'role_ids.*.exists'             => '角色資料不存在',
        ]);

        $post = $request->only(['id', 'account', 'name', 'password', 'gender', 'birthday', 'phone', 'is_active']);

        if ($isEdit) {
            # 編輯模式不接收帳號欄位，避免繞過前端唯讀限制
            unset($post['account']);
        }

        if ($validator->fails()) {
            $sessionPost = $request->only(['id', 'account', 'name', 'gender', 'birthday', 'phone', 'is_active']);
            $sessionPost['role_ids'] = $roleIds;
            $sessionPost['change_password'] = (int) $request->input('change_password', 0);

            session([self::POST_SESSION => $sessionPost]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $validator->errors()->first());
            return redirect('admin/employee/edit/' . $id);
        }

        # 編輯模式密碼為空時不更新密碼欄位
        if (empty($post['password'])) {
            unset($post['password']);
        }

        try {
            # 處理大頭照上傳
            if ($request->hasFile('avatar')) {
                # 刪除舊檔
                if ($id > 0) {
                    $oldData = $this->service->fetchDataByID($id);
                    if (!empty($oldData['avatar'])) {
                        $this->uploadService->delete($oldData['avatar']);
                    }
                }

                $post['avatar'] = $this->uploadService->upload($request->file('avatar'), 'image');
            }

            if ($id === 0) {
                # 新增
                $id = $this->service->addData($post);

                # 同步角色
                $this->service->syncRoles($id, $roleIds);

                # 記錄操作日誌
                $this->logService->recordSimple($request, 'employee', 'create', $id, $post['name'] ?? null);
            } else {
                # 取得修改前資料（若尚未取得）
                $oldData = $oldData ?? $this->service->fetchDataByID($id);

                # 編輯
                $this->service->updateData($id, $post);

                # 同步角色
                $this->service->syncRoles($id, $roleIds);

                # 記錄操作日誌
                $this->logService->recordUpdate(
                    $request,
                    'employee',
                    $id,
                    $oldData['name'] ?? null,
                    $oldData,
                    $post,
                    ['name', 'account', 'gender', 'birthday', 'phone', 'avatar', 'is_active']
                );
            }

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');
            return redirect('admin/employee/edit/' . $id);
        } catch (\Exception $e) {
            $sessionPost = $request->only(['id', 'account', 'name', 'gender', 'birthday', 'phone', 'is_active']);
            $sessionPost['role_ids'] = $roleIds;
            $sessionPost['change_password'] = (int) $request->input('change_password', 0);
            $sessionPost['avatar'] = $post['avatar'] ?? null;

            session([self::POST_SESSION => $sessionPost]);

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/employee/edit/' . $id);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $request->validate([
            'gender'    => ['nullable', 'integer', Rule::in(array_keys(config('constants.gender')))],
            'is_active' => ['nullable', 'integer', Rule::in(array_keys(config('constants.status')))],
            'birthday'  => ['nullable', 'date'],
            'phone'     => ['nullable', 'string', 'max:30'],
            'role_ids'  => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:acl_role,id'],
        ]);

        $post = $request->only(['id', 'account', 'name', 'password', 'gender', 'birthday', 'phone', 'is_active']);
        $roleIds = $request->input('role_ids', []);

        # 密碼為空時不更新密碼欄位
        if (empty($post['password'])) {
            unset($post['password']);
        }

        try {
            # 處理大頭照上傳
            if ($request->hasFile('avatar')) {
                # 刪除舊檔
                if ($post['id'] > 0) {
                    $oldData = $this->service->fetchDataByID($post['id']);
                    if (!empty($oldData['avatar'])) {
                        $this->uploadService->delete($oldData['avatar']);
                    }
                }

                $post['avatar'] = $this->uploadService->upload($request->file('avatar'), 'image');
            }

            $id = $post['id'];
            if ($post['id'] == 0) {
                # 新增
                $id = $this->service->addData($post);

                # 同步角色
                $this->service->syncRoles($id, $roleIds);

                # 記錄操作日誌
                $this->logService->recordSimple($request, 'employee', 'create', $id, $post['name'] ?? null);
            } else {
                # 取得修改前資料（若尚未取得）
                $oldData = $oldData ?? $this->service->fetchDataByID($post['id']);

                # 編輯
                $this->service->updateData($post['id'], $post);

                # 同步角色
                $this->service->syncRoles($post['id'], $roleIds);

                # 記錄操作日誌
                $this->logService->recordUpdate(
                    $request,
                    'employee',
                    $post['id'],
                    $oldData['name'] ?? null,
                    $oldData,
                    $post,
                    ['name', 'account', 'gender', 'birthday', 'phone', 'avatar', 'is_active']
                );
            }

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');
            return redirect('admin/employee/edit/' . $id);
        } catch (\Exception $e) {
            session([self::POST_SESSION => $post]);

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/employee/edit/' . ($post['id'] ?? 0));
        }
    }
}

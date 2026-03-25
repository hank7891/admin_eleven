<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AclRoleService;
use App\Services\Admin\AdminLogService;
use App\Services\Admin\AdminMenuService;
use App\Services\Share\MessageService;

class AclRoleController extends Controller
{
    const POST_SESSION = 'acl_role_edit_post';

    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(
        protected AclRoleService $service,
        protected AdminLogService $logService,
        protected AdminMenuService $menuService
    ) {
        $this->settingService = app('setting');
    }

    /**
     * 列表頁面
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function list()
    {
        # 列表欄位
        $fields = [
            'ID' => 'id',
            '角色名稱' => 'role_name',
            '建立時間' => 'created_at',
        ];

        $this->settingService->setSetData('pageTitle', '角色管理');
        $this->settingService->setSetData('editUrl', asset('admin/acl.role/edit') . '/');
        $this->settingService->setSetData('fields', $fields);
        $this->settingService->setSetData('data', $this->service->fetchAllData());

        return view('admin-share/page/list', $this->settingService->fetchSetData());
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

            # 取得選單樹（編輯頁 checkbox 用）
            $this->settingService->setSetData('menuTree', $this->menuService->fetchMenuTreeForEdit());
            $this->settingService->setSetData('data', $data);
            return view('admin/acl-role/edit', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/acl.role/list');
        }
    }

    /**
     * 編輯實作
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editDo(Request $request)
    {
        $post = $request->only(['id', 'role_name']);
        $menuIds = array_map('intval', $request->input('menu_ids', []));
        $post['menu_ids'] = $menuIds;

        try {
            $id = $post['id'];
            if ($post['id'] == 0) {
                # 新增
                $id = $this->service->addData($post);

                # 記錄操作日誌
                $this->logService->recordSimple($request, 'acl_role', 'create', $id, $post['role_name'] ?? null);
            } else {
                # 取得修改前資料
                $oldData = $this->service->fetchDataByID($post['id']);

                # 編輯
                $this->service->updateData($post['id'], $post);

                # 記錄操作日誌（角色名稱差異）
                $this->logService->recordUpdate(
                    $request,
                    'acl_role',
                    $post['id'],
                    $oldData['role_name'] ?? null,
                    $oldData,
                    $post,
                    ['role_name']
                );

                # 記錄選單權限變更
                $oldMenuIds = $oldData['menu_ids'] ?? [];
                if ($oldMenuIds != $menuIds) {
                    $this->logService->recordSimple(
                        $request,
                        'acl_role',
                        'update',
                        $post['id'],
                        $oldData['role_name'] ?? null,
                        '選單權限變更'
                    );
                }
            }

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');
            return redirect('admin/acl.role/edit/' . $id);
        } catch (\Exception $e) {
            session([self::POST_SESSION => $post]);

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/acl.role/edit/' . $id);
        }
    }
}

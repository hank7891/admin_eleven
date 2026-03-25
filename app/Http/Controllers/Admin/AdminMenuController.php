<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\Admin\AdminMenuService;
use App\Services\Admin\AdminLogService;
use App\Services\Share\MessageService;

class AdminMenuController extends Controller
{
    const POST_SESSION = 'admin_menu_edit_post';

    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(
        protected AdminMenuService $service,
        protected AdminLogService $logService
    ) {
        $this->settingService = app('setting');
    }

    /**
     * 列表頁面
     */
    public function list()
    {
        # 列表欄位
        $fields = [
            'ID'     => 'id',
            '類型'   => 'type_display',
            '所屬群組' => 'parent_display',
            '名稱'   => 'name',
            'URL'    => 'url',
            '排序'   => 'sort_order',
            '狀態'   => 'is_active_display',
            '建立時間' => 'created_at',
        ];

        $this->settingService->setSetData('pageTitle', '選單管理');
        $this->settingService->setSetData('editUrl', asset('admin/admin.menu/edit') . '/');
        $this->settingService->setSetData('fields', $fields);
        $this->settingService->setSetData('data', $this->service->fetchAllData());

        return view('admin-share/page/list', $this->settingService->fetchSetData());
    }

    /**
     * 編輯頁面
     * @param int $id
     */
    public function edit(int $id)
    {
        try {
            $data = ($id > 0) ? $this->service->fetchDataByID($id) : [];

            if (session(self::POST_SESSION)) {
                $data = session(self::POST_SESSION) + $data;
                session()->forget(self::POST_SESSION);
            }

            # 取得群組清單（供下拉選單使用）
            $groups = $this->service->fetchActiveGroups();

            $this->settingService->setSetData('data', $data);
            $this->settingService->setSetData('groups', $groups);
            return view('admin/admin-menu/edit', $this->settingService->fetchSetData());
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/admin.menu/list');
        }
    }

    /**
     * 編輯實作
     * @param Request $request
     */
    public function editDo(Request $request)
    {
        $request->validate([
            'parent_id'  => ['required', 'integer', 'min:0'],
            'name'       => ['required', 'string', 'max:100'],
            'url'        => ['nullable', 'string', 'max:255'],
            'icon'       => ['nullable', 'string', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active'  => ['required', 'integer', Rule::in(array_keys(config('constants.status')))],
        ]);

        $post = $request->only(['id', 'parent_id', 'name', 'url', 'icon', 'sort_order', 'is_active']);

        # 群組不需要 URL
        if ((int) $post['parent_id'] === 0) {
            $post['url'] = null;
        }

        # icon 預設值
        if (empty($post['icon'])) {
            $post['icon'] = (int) $post['parent_id'] === 0 ? 'fas fa-folder' : 'far fa-circle';
        }

        try {
            $id = $post['id'];

            if ($post['id'] == 0) {
                # 新增
                $id = $this->service->addData($post);
                $this->logService->recordSimple($request, 'admin_menu', 'create', $id, $post['name']);
            } else {
                # 取得修改前資料
                $oldData = $this->service->fetchDataByID($post['id']);

                # 編輯
                $this->service->updateData($post['id'], $post);

                # 記錄操作日誌
                $this->logService->recordUpdate(
                    $request,
                    'admin_menu',
                    $post['id'],
                    $oldData['name'] ?? null,
                    $oldData,
                    $post,
                    ['parent_id', 'name', 'url', 'icon', 'sort_order', 'is_active']
                );
            }

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');
            return redirect('admin/admin.menu/edit/' . $id);
        } catch (\Exception $e) {
            session([self::POST_SESSION => $post]);
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/admin.menu/edit/' . ($post['id'] ?? 0));
        }
    }

    /**
     * 刪除
     * @param Request $request
     * @param int $id
     */
    public function delete(Request $request, int $id)
    {
        try {
            $data = $this->service->fetchDataByID($id);
            $this->service->deleteData($id);

            # 記錄操作日誌
            $this->logService->recordSimple($request, 'admin_menu', 'delete', $id, $data['name']);

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '刪除成功！');
        } catch (\Exception $e) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
        }

        return redirect('admin/admin.menu/list');
    }
}

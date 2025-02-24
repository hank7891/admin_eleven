<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AclRoleService;
use App\Services\Share\MessageService;

class AclRoleController extends Controller
{
    const POST_SESSION = 'acl_role_edit_post';

    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(protected AclRoleService $service)
    {
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
}

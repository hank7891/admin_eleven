<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\Admin\EmployeeService;
use App\Services\Admin\AdminLogService;
use App\Services\Share\MessageService;

class EmployeeController extends Controller
{
    const POST_SESSION = 'employee_edit_post';

    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(
        protected EmployeeService $service,
        protected AdminLogService $logService
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
            '姓名' => 'name',
            '性別' => 'gender_display',
            '電話' => 'phone',
            '狀態' => 'is_active_display',
            '建立時間' => 'created_at',
        ];

        $this->settingService->setSetData('pageTitle', '會員管理');
        $this->settingService->setSetData('editUrl', asset('admin/employee/edit') . '/');
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

            $this->settingService->setSetData('data', $data);
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
        ]);

        $post = $request->only(['id', 'account', 'name', 'password', 'gender', 'birthday', 'phone', 'is_active']);

        # 密碼為空時不更新密碼欄位
        if (empty($post['password'])) {
            unset($post['password']);
        }

        try {
            # 處理大頭照上傳
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');

                # 白名單驗證：只允許圖片格式
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $extension = strtolower($file->getClientOriginalExtension());
                $mime = $file->getMimeType();

                if (!in_array($extension, $allowedExtensions) || !in_array($mime, $allowedMimes)) {
                    throw new \Exception('大頭照僅允許上傳圖片檔（jpg, jpeg, png, gif, webp）');
                }

                # 刪除舊檔
                if ($post['id'] > 0) {
                    $oldData = $this->service->fetchDataByID($post['id']);
                    if (!empty($oldData['avatar'])) {
                        Storage::disk('public')->delete($oldData['avatar']);
                    }
                }
                $post['avatar'] = $file->store('avatars', 'public');
            }

            $id = $post['id'];
            if ($post['id'] == 0) {
                # 新增
                $id = $this->service->addData($post);

                # 記錄操作日誌
                $this->logService->recordSimple($request, 'employee', 'create', $id, $post['name'] ?? null);
            } else {
                # 取得修改前資料（若尚未取得）
                $oldData = $oldData ?? $this->service->fetchDataByID($post['id']);

                # 編輯
                $this->service->updateData($post['id'], $post);

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

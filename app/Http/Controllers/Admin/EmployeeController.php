<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\EmployeeService;
use App\Services\Share\MessageService;

class EmployeeController extends Controller
{
    const POST_SESSION = 'employee_edit_post';

    # 建構元
    public function __construct(protected EmployeeService $service)
    {
        # code...
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
            '建立時間' => 'created_at',
        ];

        $setData = [
            'pageTitle' => '會員管理',
            'editUrl'   => asset('admin/employee/edit') . '/',
            'fields'    => $fields,
            'data'      => $this->service->fetchAllData(),
        ];
        return view('admin-share/page/list', $setData);
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
            $user = session(ADMIN_AUTH_SESSION);
            $data = ($id > 0) ? $this->service->fetchDataByID($id) : [];

            if (session(self::POST_SESSION)) {
                $data = session(self::POST_SESSION) + $data;
                session()->forget(self::POST_SESSION);
            }

            $setData = [
                'user' => $user,
                'data' => $data,
            ];

            return view('admin/employee/edit', $setData);
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
        $post = $request->all();

        try {
            $id = $post['id'];
            if ($post['id'] == 0) {
                # 新增
                $id = $this->service->addData($post);
            } else {
                # 編輯
                $this->service->updateData($post['id'], $post);
            }

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');
            return redirect('admin/employee/edit/' . $id);
        } catch (\Exception $e) {
            session([self::POST_SESSION => $post]);

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/employee/edit/' . $id);
        }
    }
}

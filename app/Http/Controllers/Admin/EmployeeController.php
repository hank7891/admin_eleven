<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\Share\Message;
use Illuminate\Http\Request;
use App\Library\Admin\Employee;

class EmployeeController extends Controller
{
    const POST_SESSION = 'employee_edit_post';

    /**
     * 編輯
     * @param Employee $employeeLib
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(Employee $employeeLib)
    {
        try {
            $user = session(ADMIN_AUTH_SESSION);
            $data = $employeeLib->fetchDataByID($user['id']);

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

            Message::setMessage(ADMIN_MESSAGE_SESSION, MESSAGE::DANGER, $e->getMessage());
            return redirect('admin/');
        }
    }

    /**
     * 編輯實作
     * @param Request  $request
     * @param Employee $employee
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editDo(Request $request, Employee $employee)
    {
        $post = $request->all();

        try {

            Message::setMessage(ADMIN_MESSAGE_SESSION, MESSAGE::SUCCESS, '編輯成功！');
            return redirect('admin/employee/edit');
        } catch (\Exception $e) {
            session([self::POST_SESSION => $post]);

            Message::setMessage(ADMIN_MESSAGE_SESSION, MESSAGE::DANGER, $e->getMessage());
            return redirect('admin/employee/edit');
        }
    }
}

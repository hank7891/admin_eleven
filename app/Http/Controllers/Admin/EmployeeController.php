<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\Share\Message;
use Illuminate\Http\Request;
use App\Library\Admin\Employee;

class EmployeeController extends Controller
{
    /**
     * 編輯
     * @param Employee $employeeLib
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(Employee $employeeLib)
    {
        try {
            $user    = session(ADMIN_AUTH_SESSION);
            $setData = [
                'user' => $user,
                'data' => $employeeLib->fetchDataByID($user['id']),
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
        try {

            Message::setMessage(ADMIN_MESSAGE_SESSION, MESSAGE::SUCCESS, '儲存成功');
            return redirect('admin/employee/edit');
        } catch (\Exception $e) {

            Message::setMessage(ADMIN_MESSAGE_SESSION, MESSAGE::DANGER, $e->getMessage());
            return redirect('admin/');
        }
    }
}

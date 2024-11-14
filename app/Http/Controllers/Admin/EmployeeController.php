<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\EmployeeService;
use App\Services\Share\MessageService;

class EmployeeController extends Controller
{
    const POST_SESSION = 'employee_edit_post';

    /**
     * 編輯
     * @param EmployeeService $employeeService
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(EmployeeService $employeeService)
    {
        try {
            $user = session(ADMIN_AUTH_SESSION);
            $data = $employeeService->fetchDataByID($user['id']);

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
     * @param EmployeeService $employeeService
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editDo(Request $request, EmployeeService $employeeService)
    {
        $post = $request->all();

        try {

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '編輯成功！');
            return redirect('admin/employee/edit');
        } catch (\Exception $e) {
            session([self::POST_SESSION => $post]);

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/employee/edit');
        }
    }
}

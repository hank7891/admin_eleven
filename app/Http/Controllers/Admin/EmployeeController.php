<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\Share\Message;
use Illuminate\Http\Request;
use App\Library\Admin\Employee;

class EmployeeController extends Controller
{
    public function edit(Employee $employeeLib)
    {
        try {
            $user = session(ADMIN_AUTH_SESSION);

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
}

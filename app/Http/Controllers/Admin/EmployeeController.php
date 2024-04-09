<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function edit()
    {
        $setData = [
            'user' => session(ADMIN_AUTH_SESSION),
        ];

        return view('admin/employee/edit', $setData);
    }
}

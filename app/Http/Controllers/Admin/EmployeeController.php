<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function edit()
    {
        echo 'Edit Employee Page';exit;
        return view('admin/employee/edit');
    }
}

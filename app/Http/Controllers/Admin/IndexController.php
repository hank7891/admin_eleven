<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Library\Admin\Auth;
use App\Library\Share\Message;

class IndexController extends Controller
{
    public function index()
    {
        return view('admin/index');
    }

    public function login()
    {
        return view('admin/login');
    }

    /**
     * 登入實作
     * @param Request $request
     * @param Auth    $auth
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function loginDo(Request $request, Auth $auth)
    {
        try {
            $account  = $request->account;
            $password = $request->password;

            if (trim($account) == '' || trim($password) == '') {
                throw new \Exception('請輸入帳號及密碼！ #002');
            }

            $employee = $auth->fetchDataByLogin($account, $password);
            session([ADMIN_AUTH_SESSION => $employee]);

            return redirect('admin/');
        } catch (\Exception $e) {

            Message::setMessage(ADMIN_MESSAGE_SESSION, MESSAGE::DANGER, $e->getMessage());
            return redirect('admin/login');
        }
    }
}

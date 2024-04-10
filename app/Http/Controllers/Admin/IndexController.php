<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AuthService;
use App\Services\Share\MessageService;

class IndexController extends Controller
{
    # 建構元
    public function __construct(protected AuthService $authService)
    {

    }

    public function index()
    {
        $setData = [
            'user' => session(ADMIN_AUTH_SESSION),
        ];
        return view('admin/index', $setData);
    }

    /**
     * 登入頁
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function login()
    {
        return view('admin/login');
    }

    /**
     * 登入實作
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function loginDo(Request $request)
    {
        try {
            $account  = $request->account;
            $password = $request->password;

            if (trim($account) == '' || trim($password) == '') {
                throw new \Exception('請輸入帳號及密碼！ #002');
            }

            $this->authService->login($account, $password);
            return redirect('admin/');
        } catch (\Exception $e) {

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/login');
        }
    }

    /**
     * 登出
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        $this->authService->logout();
        return redirect('admin/login');
    }
}

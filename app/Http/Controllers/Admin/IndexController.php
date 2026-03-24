<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AuthService;
use App\Services\Admin\AdminLoginLogService;
use App\Services\Share\MessageService;

class IndexController extends Controller
{
    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(
        protected AuthService $authService,
        protected AdminLoginLogService $loginLogService
    ) {
        $this->settingService = app('setting');
    }

    public function index()
    {
        return view('admin/index', $this->settingService->fetchSetData());
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
        $account = trim($request->account ?? '');

        try {
            $password = $request->password;

            if ($account == '' || trim($password) == '') {
                throw new \Exception('請輸入帳號及密碼！ #002');
            }

            $this->authService->login($account, $password);

            # 記錄登入成功日誌
            $employee = session(ADMIN_AUTH_SESSION);
            $this->loginLogService->recordLoginSuccess(
                $request,
                $employee['id'],
                $employee['account'],
                $employee['name']
            );

            return redirect('admin/');
        } catch (\Exception $e) {

            # 記錄登入失敗日誌
            if (!empty($account)) {
                $this->loginLogService->recordLoginFail($request, $account, $e->getMessage());
            }

            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, $e->getMessage());
            return redirect('admin/login');
        }
    }

    /**
     * 登出
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        # 記錄登出日誌（須在清除 session 前執行）
        $employee = session(ADMIN_AUTH_SESSION);
        if (!empty($employee)) {
            $this->loginLogService->recordLogout(
                $request,
                $employee['id'],
                $employee['account'],
                $employee['name']
            );
        }

        $this->authService->logout();
        return redirect('admin/login');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AuthService;
use App\Services\Admin\AdminLogService;
use App\Services\Share\MessageService;

class IndexController extends Controller
{
    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(
        protected AuthService $authService,
        protected AdminLogService $logService
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
        try {
            $account  = $request->account;
            $password = $request->password;

            if (trim($account) == '' || trim($password) == '') {
                throw new \Exception('請輸入帳號及密碼！ #002');
            }

            $this->authService->login($account, $password);

            # 記錄登入日誌（session 已寫入，可取得操作者）
            $employee = session(ADMIN_AUTH_SESSION);
            $this->logService->recordSimple($request, 'auth', 'login', $employee['id'], $employee['name']);

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
    public function logout(Request $request)
    {
        # 記錄登出日誌（須在清除 session 前執行）
        $employee = session(ADMIN_AUTH_SESSION);
        if (!empty($employee)) {
            $this->logService->recordSimple($request, 'auth', 'logout', $employee['id'], $employee['name']);
        }

        $this->authService->logout();
        return redirect('admin/login');
    }
}

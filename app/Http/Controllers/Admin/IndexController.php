<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AuthService;
use App\Services\Admin\AdminLoginLogService;
use App\Services\Admin\AdminLogService;
use App\Services\Share\MessageService;

class IndexController extends Controller
{
    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct(
        protected AuthService $authService,
        protected AdminLoginLogService $loginLogService,
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
     */
    public function login()
    {
        return view('admin/login');
    }

    /**
     * 登入實作
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

            # 依角色數決定導向
            $roles = $employee['roles'] ?? [];

            if (count($roles) === 0) {
                # 無角色：登出並導向訊息頁
                $this->authService->logout();
                session()->flash('notice_key', ADMIN_NOTICE_NO_ROLE);
                return redirect('admin/notice');
            }

            if (count($roles) > 1) {
                # 多角色：導向角色選擇頁
                return redirect('admin/select-role');
            }

            # 1 個角色：直接進入後台（已在 AuthService 自動選取）
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
     * 角色選擇頁
     */
    public function selectRole()
    {
        $employee = session(ADMIN_AUTH_SESSION);
        $roles = $employee['roles'] ?? [];

        # 無角色或僅一個角色直接導向首頁
        if (count($roles) <= 1) {
            return redirect('admin/');
        }

        return view('admin/select-role', [
            'roles'       => $roles,
            'currentRole' => session(ADMIN_ROLE_SESSION),
        ]);
    }

    /**
     * 角色選擇 / 切換實作
     */
    public function selectRoleDo(Request $request)
    {
        $request->validate([
            'role_id' => ['required', 'integer'],
        ]);

        $employee = session(ADMIN_AUTH_SESSION);
        $roles = collect($employee['roles'] ?? []);
        $roleId = (int) $request->input('role_id');

        # 驗證此角色是否屬於該帳號
        $selectedRole = $roles->firstWhere('id', $roleId);
        if (empty($selectedRole)) {
            MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::DANGER, '無此角色權限！');
            return redirect('admin/select-role');
        }

        # 記錄舊角色（用於日誌）
        $oldRole = session(ADMIN_ROLE_SESSION);

        # 設定新角色
        $this->authService->selectRole($selectedRole['id'], $selectedRole['role_name']);

        # 記錄操作日誌
        $action = empty($oldRole) ? 'create' : 'update';
        $remarks = empty($oldRole)
            ? '選擇角色：' . $selectedRole['role_name']
            : '切換角色：' . $oldRole['name'] . ' → ' . $selectedRole['role_name'];

        $this->logService->recordSimple(
            $request,
            'auth',
            $action,
            $employee['id'],
            $employee['name'],
            $remarks
        );

        MessageService::setMessage(ADMIN_MESSAGE_SESSION, MessageService::SUCCESS, '已切換至角色：' . $selectedRole['role_name']);
        return redirect('admin/');
    }

    /**
     * 後台專用訊息呈現頁
     */
    public function notice()
    {
        $msgKey = session('notice_key', '');
        $notice = config("constants.admin_notice.{$msgKey}");

        # 無有效訊息則導向登入頁
        if (empty($notice)) {
            return redirect('admin/login');
        }

        return view('Admin/notice', ['notice' => $notice]);
    }

    /**
     * 登出
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

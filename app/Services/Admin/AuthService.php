<?php

namespace App\Services\Admin;

use App\Repositories\Admin\EmployeeRepository;
use App\Models\Employee;

class AuthService
{
    # 建構元
    public function __construct(protected EmployeeRepository $employeeRepository)
    {

    }

    /**
     * 實作登入邏輯
     * @param string $account
     * @param string $password
     *
     * @throws \Exception
     */
    public function login(string $account, string $password): void
    {
        $employee = $this->employeeRepository->fetchDataByAccount($account, $password);

        if (empty($employee)) {
            throw new \Exception('帳號或密碼輸入錯誤！ #001');
        }

        # 取得帳號的角色清單
        $employeeModel = Employee::with('roles')->find($employee['id']);
        $roles = $employeeModel ? $employeeModel->roles->toArray() : [];
        $employee['roles'] = $roles;

        session([ADMIN_AUTH_SESSION => $employee]);

        # 角色只有一個時自動選取
        if (count($roles) === 1) {
            $this->selectRole($roles[0]['id'], $roles[0]['role_name']);
        }
    }

    /**
     * 選擇 / 切換角色
     * @param int $roleId
     * @param string $roleName
     */
    public function selectRole(int $roleId, string $roleName): void
    {
        session([ADMIN_ROLE_SESSION => [
            'id'   => $roleId,
            'name' => $roleName,
        ]]);

        # 載入角色的選單權限到 session
        $aclRoleService = app(AclRoleService::class);
        $menuIds = $aclRoleService->fetchMenuIdsByRoleId($roleId);
        session([ADMIN_PERMISSION_SESSION => $menuIds]);

        # 清除 URL 快取（切換角色時需重新載入）
        session()->forget('admin_allowed_urls');
    }

    /**
     * 取得當前角色
     * @return array|null
     */
    public function getCurrentRole(): ?array
    {
        return session(ADMIN_ROLE_SESSION);
    }

    /**
     * 實作登出邏輯
     */
    public function logout(): void
    {
        session()->forget(ADMIN_AUTH_SESSION);
        session()->forget(ADMIN_ROLE_SESSION);
        session()->forget(ADMIN_PERMISSION_SESSION);
        session()->forget('admin_allowed_urls');
    }
}

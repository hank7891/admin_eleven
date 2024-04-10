<?php

namespace App\Services\Admin;

use App\Repositories\Admin\EmployeeRepository;

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

        session([ADMIN_AUTH_SESSION => $employee]);
    }

    /**
     * 實作登出邏輯
     */
    public function logout(): void
    {
        session()->forget(ADMIN_AUTH_SESSION);
    }
}

<?php

namespace App\Services\Admin;

use App\Models\Employee;

class AuthService
{
    /**
     * 實作登入邏輯
     * @param string $account
     * @param string $password
     *
     * @throws \Exception
     */
    public function login(string $account, string $password): void
    {
        $employee = Employee::where('account', $account)
            ->where('password', $password)
            ->first();

        if (empty($employee)) {
            throw new \Exception('帳號或密碼輸入錯誤！ #001');
        }

        $employee = $employee->toArray();
        unset($employee['password']);
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

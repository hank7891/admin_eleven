<?php

namespace App\Library\Admin;

use App\Models\Employee;

class Auth
{
    /**
     * 依照帳號密碼資訊取得登入帳號
     * @param $account
     * @param $password
     *
     * @return mixed
     * @throws \Exception
     */
    public function fetchDataByLogin($account, $password)
    {
        $employee = Employee::where('account', $account)
            ->where('password', $password)
            ->get()
            ->toArray();

        if (empty($employee)) {
            throw new \Exception('帳號或密碼輸入錯誤！ #001');
        }

        unset($employee['password']);
        return $employee;
    }
}

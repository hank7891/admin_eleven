<?php

namespace App\Repositories\Admin;

use App\Models\Employee;

class EmployeeRepository
{
    # 建構元
    public function __construct(protected Employee $employee)
    {

    }

    /**
     * 取得所有資料
     * @return array
     */
    public function fetchAllData(): array
    {
        return $this->employee::all()->toArray();
    }

    /**
     * 依照 id 取得資料
     * @param int $id
     *
     * @return array
     * @throws \Exception
     */
    public function fetchDataByID(int $id): array
    {
        $employee = $this->employee::find($id);
        return !empty($employee) ? $employee->toArray() : [];
    }

    /**
     * 依照帳號密碼取得資料
     * @param string $account
     * @param string $password
     *
     * @return array
     */
    public function fetchDataByAccount(string $account, string $password): array
    {
        $employee = $this->employee::where('account', $account)
                        ->where('password', $password)
                        ->first();

        return !empty($employee) ? $employee->toArray() : [];
    }

    /**
     * 新增資料
     * @param array $data
     *
     * @return object
     */
    public function addData(array $data): object
    {
        return $this->employee::create($data);
    }

    /**
     * 修改資料
     * @param int   $id
     * @param array $data
     *
     * @return object
     * @throws \Exception
     */
    public function updateData(int $id, array $data): object
    {
        $employee = $this->employee::find($id);

        if (empty($employee)) {
            throw new \Exception('修改資料取得錯誤！ #001');
        }

        $employee->update($data);
        return $employee;
    }
}

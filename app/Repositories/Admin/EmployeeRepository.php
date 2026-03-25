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
     * 取得所有資料（含角色）
     * @return array
     */
    public function fetchAllData(): array
    {
        return $this->employee::with('roles')->get()->map(function ($employee) {
            $data = $employee->toArray();
            $data['role_names'] = $employee->roles->pluck('role_name')->implode(', ') ?: '--';
            return $data;
        })->toArray();
    }

    /**
     * 依照 id 取得資料（含角色 IDs）
     * @param int $id
     *
     * @return array
     * @throws \Exception
     */
    public function fetchDataByID(int $id): array
    {
        $employee = $this->employee::with('roles')->find($id);
        if (empty($employee)) {
            return [];
        }
        $data = $employee->toArray();
        $data['role_ids'] = $employee->roles->pluck('id')->toArray();
        return $data;
    }

    /**
     * 同步帳號角色
     * @param int $employeeId
     * @param array $roleIds
     */
    public function syncRoles(int $employeeId, array $roleIds): void
    {
        $employee = $this->employee::find($employeeId);
        if (!empty($employee)) {
            $employee->roles()->sync($roleIds);
        }
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

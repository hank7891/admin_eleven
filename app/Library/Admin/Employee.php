<?php

namespace App\Library\Admin;

use App\Models\Employee as EmployeeModel;

class Employee
{
    /**
     * 依照 id 取得資料
     * @param int $id
     *
     * @return array
     * @throws \Exception
     */
    public function fetchDataByID(int $id): array
    {
        $employee = EmployeeModel::find($id);

        if (empty($employee)) {
            throw new \Exception('查無資料！ #001');
        }

        unset($employee['password']);
        return $employee->toArray();
    }
}

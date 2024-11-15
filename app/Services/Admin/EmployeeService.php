<?php

namespace App\Services\Admin;

use App\Repositories\Admin\EmployeeRepository;

class EmployeeService
{
    # 建構元
    public function __construct(protected EmployeeRepository $employeeRepository)
    {

    }

    /**
     * 取得所有會員資料
     * @return array
     */
    public function fetchAllData(): array
    {
        return $this->employeeRepository->fetchAllData();
    }

    /**
     * 依照 ID 取得會員資料
     * @param int $id
     *
     * @return array
     * @throws \Exception
     */
    public function fetchDataByID(int $id): array
    {
        $data = $this->employeeRepository->fetchDataByID($id);

        if (empty($data)) {
            throw new \Exception('查無此會員資料！ #001');
        }

        return $data;
    }
}

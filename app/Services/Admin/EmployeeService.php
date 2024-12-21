<?php

namespace App\Services\Admin;

use Carbon\Carbon;
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
        $data = $this->employeeRepository->fetchAllData();

        # 資料解析
        foreach ($data as &$value) {
            # 時間資料調整
            $value['created_at'] = !empty(trim($value['created_at']))
                ? Carbon::parse($value['created_at'])->format('Y-m-d')
                : '';

            $value['updated_at'] = !empty(trim($value['updated_at']))
                ? Carbon::parse($value['updated_at'])->format('Y-m-d')
                : '';
        }

        return $data;
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

    /**
     * 新增會員帳號
     *
     * @param array $data
     *
     * @return int
     * @throws \Exception
     */
    public function addData(array $data): int
    {
        # TODO 資料驗證

        # 資料新增
        $user = $this->employeeRepository->addData($data);

        # 回傳 id
        if (empty($user->id)) {
            throw new \Exception('新增會員資料失敗！ #001');
        }

        return $user->id;
    }

    /**
     * 修改資料
     * @param int   $id
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function updateData(int $id, array $data): bool
    {
        # TODO 資料驗證

        # 資料更新
        $result = $this->employeeRepository->updateData($id, $data);

        # 回傳結果
        if (!$result) {
            throw new \Exception('更新會員資料失敗！ #001');
        }

        return $id;
    }
}

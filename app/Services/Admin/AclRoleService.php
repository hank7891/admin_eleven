<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use App\Repositories\Admin\AclRoleRepository;

class AclRoleService
{
    # 建構元
    public function __construct(protected AclRoleRepository $repository)
    {

    }

    /**
     * 取得所有會員資料
     * @return array
     */
    public function fetchAllData(): array
    {
        $data = $this->repository->fetchAllData();

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
        $data = $this->repository->fetchDataByID($id);

        if (empty($data)) {
            throw new \Exception('查無此會員資料！ #001');
        }

        return $data;
    }
}

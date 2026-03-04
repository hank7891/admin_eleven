<?php

namespace App\Repositories\Admin;

use App\Models\AclRole;

class AclRoleRepository
{
    # 建構元
    public function __construct(protected AclRole $model)
    {

    }

    /**
     * 取得所有資料
     * @return array
     */
    public function fetchAllData(): array
    {
        return $this->model::all()->toArray();
    }

    /**
     * 依照 id 取得資料
     * @param int $id
     *
     * @return array
     */
    public function fetchDataByID(int $id): array
    {
        $role = $this->model::find($id);
        return !empty($role) ? $role->toArray() : [];
    }

    /**
     * 新增資料
     * @param array $data
     *
     * @return object
     */
    public function addData(array $data): object
    {
        return $this->model::create($data);
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
        $role = $this->model::find($id);

        if (empty($role)) {
            throw new \Exception('修改資料取得錯誤！ #001');
        }

        $role->update($data);
        return $role;
    }
}

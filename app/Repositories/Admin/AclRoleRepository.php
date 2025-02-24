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
}

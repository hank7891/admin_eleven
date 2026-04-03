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
     * 取得所有角色資料
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
     * 取得分頁角色資料
     * @param array $filters
     * @param int $perPage
     * @return array ['data' => array, 'pagination' => LengthAwarePaginator]
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 20): array
    {
        $paginator = $this->repository->fetchPaginatedData($filters, $perPage);

        # 資料解析
        $data = [];
        foreach ($paginator->items() as $role) {
            $value = $role->toArray();

            $value['created_at'] = !empty(trim($value['created_at']))
                ? Carbon::parse($value['created_at'])->format('Y-m-d')
                : '';

            $value['updated_at'] = !empty(trim($value['updated_at']))
                ? Carbon::parse($value['updated_at'])->format('Y-m-d')
                : '';

            $data[] = $value;
        }

        return [
            'data' => $data,
            'pagination' => $paginator,
        ];
    }

    /**
     * 依照 ID 取得角色資料（含已綁定的選單 ID）
     * @param int $id
     *
     * @return array
     * @throws \Exception
     */
    public function fetchDataByID(int $id): array
    {
        $data = $this->repository->fetchDataByID($id);

        if (empty($data)) {
            throw new \Exception('查無此角色資料！ #001');
        }

        # 取得角色已綁定的選單 ID
        $data['menu_ids'] = $this->repository->fetchMenuIdsByRoleId($id);

        return $data;
    }

    /**
     * 新增角色
     *
     * @param array $data
     *
     * @return int
     * @throws \Exception
     */
    public function addData(array $data): int
    {
        # 分離選單 ID
        $menuIds = $data['menu_ids'] ?? [];
        unset($data['menu_ids']);

        $role = $this->repository->addData($data);

        if (empty($role->id)) {
            throw new \Exception('新增角色資料失敗！ #001');
        }

        # 同步選單權限
        $this->repository->syncMenus($role->id, $menuIds);

        return $role->id;
    }

    /**
     * 修改角色資料
     * @param int   $id
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function updateData(int $id, array $data): int
    {
        # 分離選單 ID
        $menuIds = $data['menu_ids'] ?? [];
        unset($data['menu_ids']);

        $result = $this->repository->updateData($id, $data);

        if (!$result) {
            throw new \Exception('更新角色資料失敗！ #001');
        }

        # 同步選單權限
        $this->repository->syncMenus($id, $menuIds);

        return $id;
    }

    /**
     * 取得角色已綁定的選單 ID 陣列
     * @param int $roleId
     * @return array
     */
    public function fetchMenuIdsByRoleId(int $roleId): array
    {
        return $this->repository->fetchMenuIdsByRoleId($roleId);
    }
}

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
     * 取得分頁資料（含篩選）
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->model::query();

        # 角色名稱篩選
        if (!empty($filters['role_name'])) {
            $query->where('role_name', 'like', '%' . $filters['role_name'] . '%');
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
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

    /**
     * 取得角色已綁定的選單 ID 陣列
     * @param int $roleId
     * @return array
     */
    public function fetchMenuIdsByRoleId(int $roleId): array
    {
        $role = $this->model::find($roleId);
        if (empty($role)) {
            return [];
        }

        return $role->menus()->pluck('admin_menu_id')->toArray();
    }

    /**
     * 同步角色與選單的關聯
     * @param int $roleId
     * @param array $menuIds
     */
    public function syncMenus(int $roleId, array $menuIds): void
    {
        $role = $this->model::find($roleId);
        if (!empty($role)) {
            $role->menus()->sync($menuIds);
        }
    }
}

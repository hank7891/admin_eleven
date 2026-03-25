<?php

namespace App\Repositories\Admin;

use App\Models\AdminMenu;

class AdminMenuRepository
{
    # 建構元
    public function __construct(protected AdminMenu $adminMenu)
    {

    }

    /**
     * 取得所有資料（含排序）
     * @return array
     */
    public function fetchAllData(): array
    {
        return $this->adminMenu::orderBy('parent_id')
            ->orderBy('sort_order')
            ->get()
            ->toArray();
    }

    /**
     * 取得所有啟用的群組（parent_id = 0）
     * @return array
     */
    public function fetchActiveGroups(): array
    {
        return $this->adminMenu::where('parent_id', 0)
            ->where('is_active', STATUS_ACTIVE)
            ->orderBy('sort_order')
            ->get()
            ->toArray();
    }

    /**
     * 取得啟用的選單樹狀結構
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function fetchActiveMenuTree(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->adminMenu::where('parent_id', 0)
            ->where('is_active', STATUS_ACTIVE)
            ->with(['children' => function ($query) {
                $query->where('is_active', STATUS_ACTIVE)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * 依照 ID 取得資料
     * @param int $id
     *
     * @return array
     */
    public function fetchDataByID(int $id): array
    {
        $menu = $this->adminMenu::find($id);
        return !empty($menu) ? $menu->toArray() : [];
    }

    /**
     * 新增資料
     * @param array $data
     *
     * @return object
     */
    public function addData(array $data): object
    {
        return $this->adminMenu::create($data);
    }

    /**
     * 修改資料
     * @param int $id
     * @param array $data
     *
     * @return object
     * @throws \Exception
     */
    public function updateData(int $id, array $data): object
    {
        $menu = $this->adminMenu::find($id);

        if (empty($menu)) {
            throw new \Exception('修改資料取得錯誤！');
        }

        $menu->update($data);
        return $menu;
    }

    /**
     * 刪除資料
     * @param int $id
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteData(int $id): bool
    {
        $menu = $this->adminMenu::find($id);

        if (empty($menu)) {
            throw new \Exception('刪除資料取得錯誤！');
        }

        return $menu->delete();
    }
}

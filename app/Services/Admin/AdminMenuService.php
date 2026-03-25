<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use App\Repositories\Admin\AdminMenuRepository;

class AdminMenuService
{
    # 建構元
    public function __construct(protected AdminMenuRepository $adminMenuRepository)
    {

    }

    /**
     * 取得所有選單資料（列表用）
     * @return array
     */
    public function fetchAllData(): array
    {
        $data = $this->adminMenuRepository->fetchAllData();

        # 建立群組名稱對應表
        $groupMap = [];
        foreach ($data as $item) {
            if ($item['parent_id'] === 0) {
                $groupMap[$item['id']] = $item['name'];
            }
        }

        # 資料解析
        foreach ($data as &$value) {
            $value['created_at'] = !empty(trim($value['created_at']))
                ? Carbon::parse($value['created_at'])->format('Y-m-d')
                : '';

            # 類型顯示
            $value['type_display'] = $value['parent_id'] === 0 ? '群組' : '選單項目';

            # 所屬群組顯示
            $value['parent_display'] = $value['parent_id'] === 0
                ? '--'
                : ($groupMap[$value['parent_id']] ?? '未知');

            # 啟用狀態顯示
            $value['is_active_display'] = config('constants.status')[$value['is_active'] ?? STATUS_ACTIVE]
                ?? config('constants.status.' . STATUS_INACTIVE);
        }

        return $data;
    }

    /**
     * 取得所有啟用的群組（編輯頁下拉選單用）
     * @return array
     */
    public function fetchActiveGroups(): array
    {
        return $this->adminMenuRepository->fetchActiveGroups();
    }

    /**
     * 取得啟用的選單樹狀結構（sidebar 用）
     * @return array
     */
    public function fetchMenuTree(): array
    {
        $groups = $this->adminMenuRepository->fetchActiveMenuTree();
        $menu = [];

        foreach ($groups as $group) {
            $details = [];
            foreach ($group->children as $child) {
                $details[] = [
                    'is_open' => false,
                    'name'    => $child->name,
                    'url'     => $child->url,
                    'icon'    => $child->icon,
                ];
            }

            # 群組沒有子選單時不顯示
            if (empty($details)) {
                continue;
            }

            $menu[] = [
                'have_item' => true,
                'item_name' => $group->name,
                'item_open' => false,
                'item_icon' => $group->icon,
                'details'   => $details,
            ];
        }

        return $menu;
    }

    /**
     * 依照 ID 取得選單資料
     * @param int $id
     *
     * @return array
     * @throws \Exception
     */
    public function fetchDataByID(int $id): array
    {
        $data = $this->adminMenuRepository->fetchDataByID($id);

        if (empty($data)) {
            throw new \Exception('查無此選單資料！');
        }

        return $data;
    }

    /**
     * 新增選單
     * @param array $data
     *
     * @return int
     * @throws \Exception
     */
    public function addData(array $data): int
    {
        $menu = $this->adminMenuRepository->addData($data);

        if (empty($menu->id)) {
            throw new \Exception('新增選單資料失敗！');
        }

        return $menu->id;
    }

    /**
     * 修改選單
     * @param int $id
     * @param array $data
     *
     * @return int
     * @throws \Exception
     */
    public function updateData(int $id, array $data): int
    {
        $result = $this->adminMenuRepository->updateData($id, $data);

        if (!$result) {
            throw new \Exception('更新選單資料失敗！');
        }

        return $id;
    }

    /**
     * 刪除選單
     * @param int $id
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteData(int $id): bool
    {
        # 檢查是否為群組且有子選單
        $menu = $this->adminMenuRepository->fetchDataByID($id);
        if ($menu['parent_id'] === 0) {
            $allData = $this->adminMenuRepository->fetchAllData();
            $hasChildren = collect($allData)->where('parent_id', $id)->isNotEmpty();
            if ($hasChildren) {
                throw new \Exception('此群組下仍有子選單，請先刪除子選單！');
            }
        }

        return $this->adminMenuRepository->deleteData($id);
    }
}

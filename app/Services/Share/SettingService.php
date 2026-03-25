<?php

namespace App\Services\Share;

use App\Services\Admin\AdminMenuService;

class SettingService
{
    protected array $setData = [];

    /**
     * 取得後台需要資料
     * @return array
     */
    public function fetchSetData(): array
    {
        # 放置登入者資訊
        $this->setData['user'] = session(ADMIN_AUTH_SESSION);

        # 取得動態選單
        $this->setData['menu'] = $this->fetchMenu();

        # 若無 data 欄位，則初始化為空陣列
        if (empty($this->setData['data'])) {
            $this->setData['data'] = [];
        }

        return $this->setData;
    }

    /**
     * 放置自定義資料
     * @param $key
     * @param $value
     */
    public function setSetData($key, $value): void
    {
        if (trim($key) == '') {
            return ;
        }

        $this->setData[$key] = $value;
    }

    /**
     * 從資料庫取得啟用的選單樹狀結構
     * @return array
     */
    protected function fetchMenu(): array
    {
        try {
            $menuService = app(AdminMenuService::class);
            return $menuService->fetchMenuTree();
        } catch (\Exception $e) {
            # 資料庫尚未建立時回傳空陣列
            return [];
        }
    }
}

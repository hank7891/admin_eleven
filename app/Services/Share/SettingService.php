<?php

namespace App\Services\Share;

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

        # 取得角色可操作選單
        $this->setData['menu'] = $this->setUserMenu($this->setData['user']['id']);

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
     * TODO 取得角色可操作選單
     * @param int $userId
     *
     * @return array[]
     */
    protected function setUserMenu(int $userId): array
    {
        $re = [
            1 => [
                'have_item' => true,
                'item_name' => 'Dashboard',
                'item_open' => true,
                'details'   => [
                    [
                        'is_open' => true,
                        'name'    => '會員管理',
                        'url'     => '/admin/employee/list',
                    ],
                ],

            ],
            2 => [
                'have_item' => false,
                'item_name' => '',
                'item_open' => false,
                'details'   => [
                    [
                        'is_open' => false,
                        'name' => '會員管理',
                        'url' => '/admin/employee/list',
                    ],
                ],

            ],
        ];

        return $re;
    }
}

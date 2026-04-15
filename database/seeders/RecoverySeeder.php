<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RecoverySeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdminRole();
        $this->seedEmployee();
        $this->seedRolePermissions();
        $this->seedHeroSlides();
        $this->seedProducts();
    }

    # 建立管理員角色
    private function seedAdminRole(): void
    {
        $exists = DB::table('acl_role')->where('role_name', '管理員')->exists();
        if ($exists) {
            return;
        }

        DB::table('acl_role')->insert([
            'role_name' => '管理員',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    # 建立管理員帳號 hank/8888
    private function seedEmployee(): void
    {
        $exists = DB::table('employee')->where('account', 'hank')->exists();
        if ($exists) {
            return;
        }

        $employeeId = DB::table('employee')->insertGetId([
            'account' => 'hank',
            'name' => 'Hank',
            'password' => Hash::make('8888'),
            'gender' => GENDER_UNSPECIFIED,
            'is_active' => STATUS_ACTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        # 關聯管理員角色
        $roleId = DB::table('acl_role')->where('role_name', '管理員')->value('id');
        if ($roleId) {
            $pivotExists = DB::table('employee_acl_role')
                ->where('employee_id', $employeeId)
                ->where('acl_role_id', $roleId)
                ->exists();

            if (!$pivotExists) {
                DB::table('employee_acl_role')->insert([
                    'employee_id' => $employeeId,
                    'acl_role_id' => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    # 賦予管理員角色所有選單權限
    private function seedRolePermissions(): void
    {
        $roleId = DB::table('acl_role')->where('role_name', '管理員')->value('id');
        if (!$roleId) {
            return;
        }

        # 取所有葉選單（有 url 的子選單）
        $leafMenuIds = DB::table('admin_menus')
            ->where('parent_id', '!=', 0)
            ->pluck('id');

        foreach ($leafMenuIds as $menuId) {
            $exists = DB::table('acl_role_admin_menu')
                ->where('acl_role_id', $roleId)
                ->where('admin_menu_id', $menuId)
                ->exists();

            if (!$exists) {
                DB::table('acl_role_admin_menu')->insert([
                    'acl_role_id' => $roleId,
                    'admin_menu_id' => $menuId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    # 補輪播資料（依據現有圖片檔案）
    private function seedHeroSlides(): void
    {
        if (DB::table('hero_slides')->count() > 0) {
            return;
        }

        $employeeId = DB::table('employee')->where('account', 'hank')->value('id');
        $now = now();

        $slides = [
            [
                'image_path' => 'uploads/image/2026/04/a17a1180-7608-4fb8-aba8-b126c00a7457.jpg',
                'image_alt' => '鄉村風光',
                'title' => '鄉村風光',
                'description' => '陽光明媚的田園風景',
                'sort_order' => 1,
            ],
            [
                'image_path' => 'uploads/image/2026/04/7fb4ec97-e1e2-4141-8b6c-22b119c530eb.jpg',
                'image_alt' => '城市夜景',
                'title' => '城市夜景',
                'description' => '繁華都市的夜間街景',
                'sort_order' => 2,
            ],
        ];

        foreach ($slides as $slide) {
            DB::table('hero_slides')->insert(array_merge($slide, [
                'eyebrow' => null,
                'primary_cta_label' => null,
                'primary_cta_url' => null,
                'secondary_cta_label' => null,
                'secondary_cta_url' => null,
                'is_active' => STATUS_ACTIVE,
                'start_at' => $now,
                'end_at' => null,
                'created_by' => $employeeId,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    # 補商品資料（依據現有圖片檔案）
    private function seedProducts(): void
    {
        if (DB::table('products')->count() > 0) {
            return;
        }

        $employeeId = DB::table('employee')->where('account', 'hank')->value('id');
        $now = now();

        # 建立商品類別
        $categoryId = DB::table('product_categories')->insertGetId([
            'name' => '公仔模型',
            'sort_order' => 1,
            'is_active' => STATUS_ACTIVE,
            'created_by' => $employeeId,
            'updated_by' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        # 商品 1：RB-X1 戰鬥機器人
        $product1Id = DB::table('products')->insertGetId([
            'name' => 'RB-X1 戰鬥機器人',
            'tagline' => '極致戰鬥體驗',
            'price' => 2980,
            'description' => 'RB-X1 是一款精密設計的戰鬥型機器人公仔，具備多種姿態展示模式，深色金屬質感外殼搭配 LED 指示燈細節，適合收藏與桌面展示。',
            'category_id' => $categoryId,
            'status_key' => (string) PRODUCT_STATUS_ONLINE,
            'is_featured' => PRODUCT_FEATURED_ON,
            'sort_order' => 1,
            'start_at' => $now,
            'end_at' => null,
            'created_by' => $employeeId,
            'updated_by' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        # RB-X1 圖片
        $product1Images = [
            ['path' => 'uploads/image/2026/04/518494dc-c016-42e2-be7f-8e5821e7225c.jpg', 'alt' => 'RB-X1 包裝展示', 'primary' => 1, 'sort' => 1],
            ['path' => 'uploads/image/2026/04/a5de0ada-9fdd-4504-a378-7c6d03974d36.jpg', 'alt' => 'RB-X1 本體正面', 'primary' => 0, 'sort' => 2],
            ['path' => 'uploads/image/2026/04/be19b1e0-f819-44b4-8027-b88f849291d6.jpg', 'alt' => 'RB-X1 戰鬥模式', 'primary' => 0, 'sort' => 3],
        ];

        foreach ($product1Images as $img) {
            DB::table('product_images')->insert([
                'product_id' => $product1Id,
                'image_path' => $img['path'],
                'image_alt' => $img['alt'],
                'is_primary' => $img['primary'],
                'sort_order' => $img['sort'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        # 商品 2：FR-01 青蛙公仔
        $product2Id = DB::table('products')->insertGetId([
            'name' => 'FR-01 青蛙公仔',
            'tagline' => '療癒系收藏首選',
            'price' => 1580,
            'description' => 'FR-01 青蛙公仔系列以可愛療癒風格為主打，圓潤造型搭配皇冠裝飾，限量版附贈星星配件，是桌面擺飾與送禮的人氣選擇。',
            'category_id' => $categoryId,
            'status_key' => (string) PRODUCT_STATUS_ONLINE,
            'is_featured' => PRODUCT_FEATURED_OFF,
            'sort_order' => 2,
            'start_at' => $now,
            'end_at' => null,
            'created_by' => $employeeId,
            'updated_by' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        # FR-01 圖片
        $product2Images = [
            ['path' => 'uploads/image/2026/04/5b25e004-7501-4651-9142-09a42cac0994.jpg', 'alt' => 'Frog King 限量版', 'primary' => 1, 'sort' => 1],
            ['path' => 'uploads/image/2026/04/ac78d2f5-5377-428c-91bf-1b9ee2ff2ec4.jpg', 'alt' => 'FR-01 奔跑姿態', 'primary' => 0, 'sort' => 2],
            ['path' => 'uploads/image/2026/04/0924469e-91f0-4407-b12f-8e655f411c3b.jpg', 'alt' => 'FR-01 皇冠坐姿', 'primary' => 0, 'sort' => 3],
        ];

        foreach ($product2Images as $img) {
            DB::table('product_images')->insert([
                'product_id' => $product2Id,
                'image_path' => $img['path'],
                'image_alt' => $img['alt'],
                'is_primary' => $img['primary'],
                'sort_order' => $img['sort'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}

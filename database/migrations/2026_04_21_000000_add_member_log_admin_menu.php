<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_menus') || !Schema::hasTable('acl_role') || !Schema::hasTable('acl_role_admin_menu')) {
            return;
        }

        # 將會員相關日誌歸類到「系統」分類底下，與既有後台「操作日誌 / 登入日誌」並列
        $parent = DB::table('admin_menus')
            ->where('parent_id', 0)
            ->where('name', '系統')
            ->first();

        if (empty($parent)) {
            return;
        }

        $now = now();

        $menus = [
            [
                'name' => '會員登入日誌',
                'url' => '/admin/member.login-log/list',
                'icon' => 'far fa-circle',
            ],
            [
                'name' => '會員操作日誌',
                'url' => '/admin/member.operation-log/list',
                'icon' => 'far fa-circle',
            ],
        ];

        foreach ($menus as $item) {
            $menu = DB::table('admin_menus')
                ->where('parent_id', $parent->id)
                ->where('url', $item['url'])
                ->first();

            if (empty($menu)) {
                $nextSortOrder = (int) (DB::table('admin_menus')
                    ->where('parent_id', $parent->id)
                    ->max('sort_order') ?? 0) + 1;

                $menuId = DB::table('admin_menus')->insertGetId([
                    'parent_id' => $parent->id,
                    'name' => $item['name'],
                    'url' => $item['url'],
                    'icon' => $item['icon'],
                    'sort_order' => $nextSortOrder,
                    'is_active' => STATUS_ACTIVE,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $menuId = (int) $menu->id;
            }

            $roleIds = DB::table('acl_role')->pluck('id');
            foreach ($roleIds as $roleId) {
                $exists = DB::table('acl_role_admin_menu')
                    ->where('acl_role_id', $roleId)
                    ->where('admin_menu_id', $menuId)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('acl_role_admin_menu')->insert([
                    'acl_role_id' => $roleId,
                    'admin_menu_id' => $menuId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('admin_menus') || !Schema::hasTable('acl_role_admin_menu')) {
            return;
        }

        $urls = [
            '/admin/member.login-log/list',
            '/admin/member.operation-log/list',
        ];

        foreach ($urls as $url) {
            $menu = DB::table('admin_menus')
                ->where('url', $url)
                ->first();

            if (empty($menu)) {
                continue;
            }

            DB::table('acl_role_admin_menu')
                ->where('admin_menu_id', $menu->id)
                ->delete();

            DB::table('admin_menus')
                ->where('id', $menu->id)
                ->delete();
        }
    }
};

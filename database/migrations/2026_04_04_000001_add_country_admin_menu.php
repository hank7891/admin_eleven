<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        # 取得 Dashboard 群組
        $dashboard = DB::table('admin_menus')
            ->where('parent_id', 0)
            ->where('name', 'Dashboard')
            ->first();

        if (empty($dashboard)) {
            return;
        }

        # 避免重複新增選單
        $menu = DB::table('admin_menus')
            ->where('parent_id', $dashboard->id)
            ->where('url', '/admin/country/list')
            ->first();

        if (empty($menu)) {
            $menuId = DB::table('admin_menus')->insertGetId([
                'parent_id'  => $dashboard->id,
                'name'       => '國別管理',
                'url'        => '/admin/country/list',
                'icon'       => 'far fa-circle',
                'sort_order' => 3,
                'is_active'  => STATUS_ACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $menuId = $menu->id;
        }

        # 將新選單授權給現有角色，避免模組建立後無人可進入
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
                'acl_role_id'   => $roleId,
                'admin_menu_id' => $menuId,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }

    public function down(): void
    {
        $menu = DB::table('admin_menus')
            ->where('url', '/admin/country/list')
            ->first();

        if (empty($menu)) {
            return;
        }

        DB::table('acl_role_admin_menu')
            ->where('admin_menu_id', $menu->id)
            ->delete();

        DB::table('admin_menus')
            ->where('id', $menu->id)
            ->delete();
    }
};


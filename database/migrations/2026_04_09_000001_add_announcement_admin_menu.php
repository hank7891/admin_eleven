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
            ->where('url', '/admin/announcement/list')
            ->first();

        if (empty($menu)) {
            $menuId = DB::table('admin_menus')->insertGetId([
                'parent_id'  => $dashboard->id,
                'name'       => '公告管理',
                'url'        => '/admin/announcement/list',
                'icon'       => 'far fa-circle',
                'sort_order' => 4,
                'is_active'  => STATUS_ACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $menuId = $menu->id;
        }

        # 自動授權給現有角色
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
        if (!Schema::hasTable('admin_menus') || !Schema::hasTable('acl_role_admin_menu')) {
            return;
        }

        $menu = DB::table('admin_menus')
            ->where('url', '/admin/announcement/list')
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



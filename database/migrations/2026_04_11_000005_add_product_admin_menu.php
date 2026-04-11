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

        $group = DB::table('admin_menus')
            ->where('parent_id', 0)
            ->where('name', '商品管理')
            ->first();

        if (empty($group)) {
            $groupId = DB::table('admin_menus')->insertGetId([
                'parent_id' => 0,
                'name' => '商品管理',
                'url' => null,
                'icon' => 'fas fa-box-open',
                'sort_order' => 4,
                'is_active' => STATUS_ACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $groupId = $group->id;
        }

        $menus = [
            ['name' => '商品列表', 'url' => '/admin/product/list', 'sort_order' => 1],
            ['name' => '類別管理', 'url' => '/admin/product.category/list', 'sort_order' => 2],
            ['name' => '標籤管理', 'url' => '/admin/product.tag/list', 'sort_order' => 3],
        ];

        foreach ($menus as $menu) {
            $item = DB::table('admin_menus')
                ->where('parent_id', $groupId)
                ->where('url', $menu['url'])
                ->first();

            if (empty($item)) {
                $menuId = DB::table('admin_menus')->insertGetId([
                    'parent_id' => $groupId,
                    'name' => $menu['name'],
                    'url' => $menu['url'],
                    'icon' => 'far fa-circle',
                    'sort_order' => $menu['sort_order'],
                    'is_active' => STATUS_ACTIVE,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $menuId = $item->id;
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

        $urls = ['/admin/product/list', '/admin/product.category/list', '/admin/product.tag/list'];

        $menuIds = DB::table('admin_menus')->whereIn('url', $urls)->pluck('id');

        if ($menuIds->isNotEmpty()) {
            DB::table('acl_role_admin_menu')->whereIn('admin_menu_id', $menuIds)->delete();
            DB::table('admin_menus')->whereIn('id', $menuIds)->delete();
        }

        $group = DB::table('admin_menus')
            ->where('parent_id', 0)
            ->where('name', '商品管理')
            ->first();

        if (empty($group)) {
            return;
        }

        $hasChildren = DB::table('admin_menus')->where('parent_id', $group->id)->exists();
        if (!$hasChildren) {
            DB::table('admin_menus')->where('id', $group->id)->delete();
        }
    }
};


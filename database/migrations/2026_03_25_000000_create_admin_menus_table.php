<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父層 ID，0 表示群組');
            $table->string('name', 100)->comment('選單名稱');
            $table->string('url', 255)->nullable()->comment('連結路徑，群組為 null');
            $table->string('icon', 100)->default('far fa-circle')->comment('圖示 class');
            $table->integer('sort_order')->default(0)->comment('排序，數字越小越前面');
            $table->tinyInteger('is_active')->default(STATUS_ACTIVE)->comment('啟用狀態');
            $table->timestamps();

            $table->index('parent_id');
            $table->index('sort_order');
            $table->index('is_active');
        });

        # 寫入現有選單資料
        $this->seedMenus();
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_menus');
    }

    /**
     * 寫入既有選單資料
     */
    protected function seedMenus(): void
    {
        $now = now();

        # 群組：Dashboard
        $dashboardId = DB::table('admin_menus')->insertGetId([
            'parent_id'  => 0,
            'name'       => 'Dashboard',
            'url'        => null,
            'icon'       => 'fas fa-tachometer-alt',
            'sort_order' => 1,
            'is_active'  => STATUS_ACTIVE,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('admin_menus')->insert([
            [
                'parent_id'  => $dashboardId,
                'name'       => '角色管理',
                'url'        => '/admin/acl.role/list',
                'icon'       => 'far fa-circle',
                'sort_order' => 1,
                'is_active'  => STATUS_ACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'parent_id'  => $dashboardId,
                'name'       => '會員管理',
                'url'        => '/admin/employee/list',
                'icon'       => 'far fa-circle',
                'sort_order' => 2,
                'is_active'  => STATUS_ACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        # 群組：小遊戲
        $gameId = DB::table('admin_menus')->insertGetId([
            'parent_id'  => 0,
            'name'       => '小遊戲',
            'url'        => null,
            'icon'       => 'fas fa-gamepad',
            'sort_order' => 2,
            'is_active'  => STATUS_ACTIVE,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('admin_menus')->insert([
            'parent_id'  => $gameId,
            'name'       => '貪食蛇',
            'url'        => '/admin/game.snake/',
            'icon'       => 'far fa-circle',
            'sort_order' => 1,
            'is_active'  => STATUS_ACTIVE,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        # 群組：系統
        $systemId = DB::table('admin_menus')->insertGetId([
            'parent_id'  => 0,
            'name'       => '系統',
            'url'        => null,
            'icon'       => 'fas fa-cogs',
            'sort_order' => 3,
            'is_active'  => STATUS_ACTIVE,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('admin_menus')->insert([
            [
                'parent_id'  => $systemId,
                'name'       => '操作日誌',
                'url'        => '/admin/admin.log/list',
                'icon'       => 'far fa-circle',
                'sort_order' => 1,
                'is_active'  => STATUS_ACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'parent_id'  => $systemId,
                'name'       => '登入日誌',
                'url'        => '/admin/admin.login-log/list',
                'icon'       => 'far fa-circle',
                'sort_order' => 2,
                'is_active'  => STATUS_ACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
};

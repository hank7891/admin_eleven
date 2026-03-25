<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acl_role_admin_menu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acl_role_id')->comment('角色 ID');
            $table->unsignedBigInteger('admin_menu_id')->comment('選單 ID');
            $table->timestamps();

            $table->unique(['acl_role_id', 'admin_menu_id']);
            $table->index('acl_role_id');
            $table->index('admin_menu_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acl_role_admin_menu');
    }
};

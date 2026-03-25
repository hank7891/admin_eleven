<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_acl_role', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->comment('帳號 ID');
            $table->unsignedBigInteger('acl_role_id')->comment('角色 ID');
            $table->timestamps();

            $table->unique(['employee_id', 'acl_role_id']);
            $table->index('employee_id');
            $table->index('acl_role_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_acl_role');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        # 若欄位已由 create_employee_table 建立則跳過
        if (!Schema::hasColumn('employee', 'gender')) {
            Schema::table('employee', function (Blueprint $table) {
                $table->tinyInteger('gender')->nullable()->comment('0:未指定 1:男 2:女');
            });
        }

        if (!Schema::hasColumn('employee', 'birthday')) {
            Schema::table('employee', function (Blueprint $table) {
                $table->date('birthday')->nullable();
            });
        }

        if (!Schema::hasColumn('employee', 'phone')) {
            Schema::table('employee', function (Blueprint $table) {
                $table->string('phone', 30)->nullable();
            });
        }

        if (!Schema::hasColumn('employee', 'avatar')) {
            Schema::table('employee', function (Blueprint $table) {
                $table->string('avatar', 255)->nullable()->comment('大頭照相對路徑');
            });
        }
    }

    public function down(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->dropColumn(['gender', 'birthday', 'phone', 'avatar']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        # 若欄位已由 create_employee_table 建立則跳過
        if (!Schema::hasColumn('employee', 'is_active')) {
            Schema::table('employee', function (Blueprint $table) {
                $table->tinyInteger('is_active')->default(1)->comment('是否啟用 1:啟用 0:停用');
            });
        }
    }

    public function down(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->id();
            $table->string('account')->unique()->comment('登入帳號');
            $table->string('name')->comment('姓名');
            $table->string('password')->comment('密碼（bcrypt）');
            $table->tinyInteger('gender')->nullable()->default(GENDER_UNSPECIFIED)->comment('性別(enum-GENDER)');
            $table->date('birthday')->nullable()->comment('生日');
            $table->string('phone', 30)->nullable()->comment('電話');
            $table->string('avatar', 255)->nullable()->comment('大頭照相對路徑');
            $table->tinyInteger('is_active')->default(STATUS_ACTIVE)->comment('是否啟用(enum-STATUS)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};

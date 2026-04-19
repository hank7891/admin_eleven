<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member', function (Blueprint $table) {
            $table->id()->comment('會員主鍵');
            $table->string('email', 255)->unique()->comment('登入帳號(email)');
            $table->string('password', 255)->comment('密碼雜湊值');
            $table->string('name', 100)->comment('會員姓名');
            $table->string('phone', 30)->nullable()->comment('手機號碼');
            $table->date('birthday')->nullable()->comment('生日日期');
            $table->string('gender_key', 20)
                ->default((string) GENDER_UNSPECIFIED)
                ->comment('性別(enum-GENDER)');
            $table->string('avatar_path', 500)->nullable()->comment('大頭照路徑');
            $table->string('status_key', 20)
                ->default('active')
                ->comment('帳號狀態(enum-MEMBER_STATUS)');
            $table->timestamp('email_verified_at')->nullable()->comment('Email 驗證時間');
            $table->timestamp('last_login_at')->nullable()->comment('最後登入時間');
            $table->string('last_login_ip', 45)->nullable()->comment('最後登入IP');
            $table->string('registered_ip', 45)->nullable()->comment('註冊來源IP');
            $table->timestamps();

            $table->index('status_key');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member');
    }
};


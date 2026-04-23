<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_login_logs', function (Blueprint $table) {
            $table->id()->comment('主鍵');
            $table->unsignedBigInteger('member_id')->nullable()->comment('關聯會員資料(member)，登入失敗時可能為 null');
            $table->string('account', 255)->comment('登入帳號 email（反正規化）');
            $table->string('member_name', 100)->nullable()->comment('會員姓名（反正規化）');
            $table->string('action', 20)->comment('操作類型(enum-MEMBER_LOGIN_LOG_ACTION)：login / logout / register');
            $table->tinyInteger('status')->default(MEMBER_LOGIN_LOG_STATUS_SUCCESS)
                ->comment('狀態(enum-MEMBER_LOGIN_LOG_STATUS)：1=成功 0=失敗');
            $table->string('fail_reason', 255)->nullable()->comment('失敗原因');
            $table->string('ip_address', 45)->comment('IP 位址');
            $table->string('user_agent', 500)->nullable()->comment('瀏覽器 UA（協助辨識異常登入）');
            $table->timestamp('operated_at')->comment('操作時間');
            $table->timestamps();

            $table->index('member_id');
            $table->index('action');
            $table->index('status');
            $table->index('operated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_login_logs');
    }
};

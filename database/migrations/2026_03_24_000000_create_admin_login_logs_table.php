<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_login_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable()->comment('關聯帳號 ID，失敗時可能為 null');
            $table->string('account', 100)->comment('登入帳號（反正規化）');
            $table->string('employee_name', 100)->nullable()->comment('帳號姓名（反正規化）');
            $table->string('action', 20)->comment('操作類型：login / logout');
            $table->tinyInteger('status')->default(LOGIN_LOG_STATUS_SUCCESS)->comment('狀態：1=成功 0=失敗');
            $table->string('fail_reason', 255)->nullable()->comment('失敗原因');
            $table->string('ip_address', 45)->comment('IP 位址');
            $table->timestamp('operated_at')->comment('操作時間');
            $table->timestamps();

            $table->index('employee_id');
            $table->index('action');
            $table->index('status');
            $table->index('operated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_login_logs');
    }
};

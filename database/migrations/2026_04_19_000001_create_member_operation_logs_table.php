<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_operation_logs', function (Blueprint $table) {
            $table->id()->comment('主鍵');
            $table->unsignedBigInteger('member_id')->comment('關聯會員資料(member)，操作者');
            $table->string('operator_name', 255)->comment('操作者名稱（反正規化）');
            $table->string('ip_address', 45)->comment('操作者 IP');
            $table->string('module', 100)->comment('操作模組(enum-MEMBER_LOG_MODULE)');
            $table->string('action', 50)->comment('操作行為(enum-MEMBER_LOG_ACTION)：create/update/delete');
            $table->unsignedBigInteger('target_id')->nullable()->comment('被操作資源 ID（通常等於 member_id）');
            $table->string('target_name', 255)->nullable()->comment('被操作資源名稱');
            $table->longText('changes')->comment('修改內容 JSON');
            $table->text('remarks')->nullable()->comment('操作備註');
            $table->timestamp('operated_at')->comment('操作時間')->useCurrent();
            $table->timestamps();

            $table->index('member_id');
            $table->index('module');
            $table->index('action');
            $table->index('operated_at');
            $table->index(['module', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_operation_logs');
    }
};


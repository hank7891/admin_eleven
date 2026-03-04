<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();

            // 操作者信息
            $table->foreignId('employee_id')->constrained('employee')->onDelete('cascade');
            $table->string('operator_name', 255)->comment('操作者名稱');

            // 請求信息
            $table->string('ip_address', 45)->comment('操作者IP');
            $table->string('module', 100)->comment('操作模組（如: employee, acl_role）');
            $table->string('action', 50)->comment('操作行為（create, update, delete）');

            // 操作詳情
            $table->integer('target_id')->nullable()->comment('被操作的資源ID');
            $table->string('target_name', 255)->nullable()->comment('被操作資源名稱');
            $table->longText('changes')->comment('修改內容（JSON 格式）');
            $table->text('remarks')->nullable()->comment('操作備註');

            // 時間戳記
            $table->timestamp('operated_at')->comment('操作時間')->useCurrent();
            $table->timestamps();

            // 索引優化
            $table->index('employee_id');
            $table->index('module');
            $table->index('action');
            $table->index('operated_at');
            $table->index(['module', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment('公告類型：1=全系統公告, 2=一般公告');
            $table->string('title', 200)->comment('標題');
            $table->string('summary', 500)->nullable()->comment('大綱');
            $table->text('content')->comment('內文（純文字）');
            $table->tinyInteger('is_active')->default(STATUS_ACTIVE)->comment('開放狀態');
            $table->dateTime('start_at')->comment('開始時間');
            $table->dateTime('end_at')->nullable()->comment('結束時間，null 表示永久');
            $table->unsignedBigInteger('created_by')->comment('建立者');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('最後編輯者');
            $table->timestamps();

            $table->index(['type', 'is_active', 'start_at', 'end_at'], 'announcements_frontend_idx');
            $table->index('type');
            $table->index('is_active');
            $table->index('start_at');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};



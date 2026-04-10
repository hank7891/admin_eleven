<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('image_path', 500)->comment('圖片儲存路徑');
            $table->string('image_alt', 200)->nullable()->comment('圖片替代文字');
            $table->string('eyebrow', 100)->nullable()->comment('眉標文字');
            $table->string('title', 200)->comment('主標語');
            $table->string('description', 500)->nullable()->comment('說明文字');
            $table->string('primary_cta_label', 50)->nullable()->comment('主按鈕文字');
            $table->string('primary_cta_url', 500)->nullable()->comment('主按鈕連結');
            $table->string('secondary_cta_label', 50)->nullable()->comment('次按鈕文字');
            $table->string('secondary_cta_url', 500)->nullable()->comment('次按鈕連結');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->tinyInteger('is_active')->default(STATUS_ACTIVE)->comment('開放狀態');
            $table->dateTime('start_at')->comment('開始呈現時間');
            $table->dateTime('end_at')->nullable()->comment('結束呈現時間');
            $table->unsignedBigInteger('created_by')->comment('建立者');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('最後編輯者');
            $table->timestamps();

            $table->index(['is_active', 'start_at', 'end_at', 'sort_order'], 'hero_slides_frontend_idx');
            $table->index(['sort_order', 'id'], 'hero_slides_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->comment('關聯商品資料(products)');
            $table->string('image_path', 500)->comment('圖片儲存路徑');
            $table->string('image_alt', 200)->nullable()->comment('圖片替代文字');
            $table->tinyInteger('is_primary')->default(0)->comment('是否為標題圖片(0/1)');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'sort_order']);
            $table->index(['product_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};


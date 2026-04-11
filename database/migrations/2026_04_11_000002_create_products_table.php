<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->comment('商品名稱');
            $table->string('tagline', 100)->nullable()->comment('商品標語');
            $table->unsignedInteger('price')->comment('售價（台幣整數）');
            $table->longText('description')->comment('商品描述');
            $table->unsignedBigInteger('category_id')->nullable()->comment('關聯商品類別資料(product_categories)');
            $table->string('status_key', 20)->default((string) PRODUCT_STATUS_OFFLINE)->comment('商品上下架(enum-PRODUCT_STATUS)');
            $table->tinyInteger('is_featured')->default(PRODUCT_FEATURED_OFF)->comment('商品主打(enum-PRODUCT_FEATURED)');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->dateTime('start_at')->comment('開始上架時間');
            $table->dateTime('end_at')->nullable()->comment('結束上架時間');
            $table->unsignedBigInteger('created_by')->nullable()->comment('關聯帳號資料(employee)');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('關聯帳號資料(employee)');
            $table->timestamps();

            $table->index(['status_key', 'start_at', 'end_at', 'sort_order'], 'products_status_period_idx');
            $table->index(['category_id', 'status_key'], 'products_category_status_idx');
            $table->index(['is_featured', 'sort_order'], 'products_featured_sort_idx');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};


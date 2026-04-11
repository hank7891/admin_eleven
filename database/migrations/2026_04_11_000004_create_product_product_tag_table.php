<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_product_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->comment('關聯商品資料(products)');
            $table->unsignedBigInteger('product_tag_id')->comment('關聯商品標籤資料(product_tags)');
            $table->timestamps();

            $table->unique(['product_id', 'product_tag_id']);
            $table->index('product_tag_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_tag_id')->references('id')->on('product_tags')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_product_tag');
    }
};


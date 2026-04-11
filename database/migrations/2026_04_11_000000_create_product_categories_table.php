<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('類別名稱');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->tinyInteger('is_active')->default(STATUS_ACTIVE)->comment('狀態(enum-STATUS)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('關聯帳號資料(employee)');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('關聯帳號資料(employee)');
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};


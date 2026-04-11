<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('標籤名稱');
            $table->tinyInteger('is_active')->default(STATUS_ACTIVE)->comment('狀態(enum-STATUS)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('關聯帳號資料(employee)');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('關聯帳號資料(employee)');
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_tags');
    }
};


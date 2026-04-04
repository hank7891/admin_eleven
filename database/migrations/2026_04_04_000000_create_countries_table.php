<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('國家名稱');
            $table->string('abbreviation', 10)->nullable()->comment('國家縮寫');
            $table->string('country_code', 20)->comment('國家代碼');
            $table->tinyInteger('is_active')->default(STATUS_ACTIVE)->comment('啟用狀態');
            $table->timestamps();

            $table->unique('country_code');
            $table->index('name');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};


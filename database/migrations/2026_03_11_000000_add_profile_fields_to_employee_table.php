<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->tinyInteger('gender')->nullable()->comment('0:未指定 1:男 2:女');
            $table->date('birthday')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('avatar', 255)->nullable()->comment('大頭照相對路徑');
        });
    }

    public function down(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->dropColumn(['gender', 'birthday', 'phone', 'avatar']);
        });
    }
};

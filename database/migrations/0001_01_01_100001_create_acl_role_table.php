<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acl_role', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 100)->comment('角色名稱');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acl_role');
    }
};

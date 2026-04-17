<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_page', function (Blueprint $table) {
            $table->id()->comment('關於我們單例主鍵（固定 1）');
            $table->string('hero_title', 100)->comment('Hero 主標題');
            $table->string('hero_subtitle', 300)->nullable()->comment('Hero 副標題');
            $table->string('hero_image_path', 500)->nullable()->comment('Hero 主視覺圖路徑');
            $table->string('story_title', 100)->comment('品牌故事標題');
            $table->text('story_content')->comment('品牌故事內文（純文字）');
            $table->string('mission_title', 100)->nullable()->comment('使命標題');
            $table->text('mission_content')->nullable()->comment('使命內文（純文字）');
            $table->string('vision_title', 100)->nullable()->comment('願景標題');
            $table->text('vision_content')->nullable()->comment('願景內文（純文字）');
            $table->string('contact_email', 255)->nullable()->comment('聯絡 email');
            $table->string('contact_phone', 50)->nullable()->comment('聯絡電話');
            $table->string('contact_address', 500)->nullable()->comment('聯絡地址');
            $table->string('meta_description', 300)->nullable()->comment('SEO 描述');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('關聯帳號資料(employee)');
            $table->timestamps();
        });

        if (!DB::table('about_page')->where('id', 1)->exists()) {
            DB::table('about_page')->insert([
                'id' => 1,
                'hero_title' => 'Aura & Heirloom',
                'hero_subtitle' => '為日常留一個慢下來的位置',
                'story_title' => '品牌故事',
                'story_content' => '請在後台編輯關於我們內容。',
                'mission_title' => null,
                'mission_content' => null,
                'vision_title' => null,
                'vision_content' => null,
                'contact_email' => null,
                'contact_phone' => null,
                'contact_address' => null,
                'meta_description' => null,
                'hero_image_path' => null,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('about_page');
    }
};


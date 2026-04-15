<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('hero_slides', 'target_url')) {
            Schema::table('hero_slides', function (Blueprint $table) {
                $table->string('target_url', 500)
                    ->nullable()
                    ->after('description')
                    ->comment('輪播點擊連結');
            });
        }

        # 先搬移舊資料，避免刪欄位時遺失連結
        if (Schema::hasColumn('hero_slides', 'primary_cta_url') && Schema::hasColumn('hero_slides', 'target_url')) {
            DB::statement("UPDATE hero_slides SET target_url = COALESCE(NULLIF(target_url, ''), NULLIF(primary_cta_url, ''), NULLIF(secondary_cta_url, ''))");
        }

        if (Schema::hasColumn('hero_slides', 'primary_cta_label')) {
            Schema::table('hero_slides', function (Blueprint $table) {
                $table->dropColumn('primary_cta_label');
            });
        }

        if (Schema::hasColumn('hero_slides', 'primary_cta_url')) {
            Schema::table('hero_slides', function (Blueprint $table) {
                $table->dropColumn('primary_cta_url');
            });
        }

        if (Schema::hasColumn('hero_slides', 'secondary_cta_label')) {
            Schema::table('hero_slides', function (Blueprint $table) {
                $table->dropColumn('secondary_cta_label');
            });
        }

        if (Schema::hasColumn('hero_slides', 'secondary_cta_url')) {
            Schema::table('hero_slides', function (Blueprint $table) {
                $table->dropColumn('secondary_cta_url');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('hero_slides', 'primary_cta_label')) {
            Schema::table('hero_slides', function (Blueprint $table) {
                $table->string('primary_cta_label', 50)
                    ->nullable()
                    ->after('description')
                    ->comment('主按鈕文字');
            });
        }

        if (!Schema::hasColumn('hero_slides', 'primary_cta_url')) {
            Schema::table('hero_slides', function (Blueprint $table) {
                $table->string('primary_cta_url', 500)
                    ->nullable()
                    ->after('primary_cta_label')
                    ->comment('主按鈕連結');
            });
        }

        # rollback 時把 target_url 回填到 primary_cta_url
        if (Schema::hasColumn('hero_slides', 'target_url') && Schema::hasColumn('hero_slides', 'primary_cta_url')) {
            DB::statement("UPDATE hero_slides SET primary_cta_url = COALESCE(NULLIF(primary_cta_url, ''), NULLIF(target_url, ''))");
        }

        if (!Schema::hasColumn('hero_slides', 'secondary_cta_label')) {
            Schema::table('hero_slides', function (Blueprint $table) {
                $table->string('secondary_cta_label', 50)
                    ->nullable()
                    ->after('primary_cta_url')
                    ->comment('次按鈕文字');
            });
        }

        if (!Schema::hasColumn('hero_slides', 'secondary_cta_url')) {
            Schema::table('hero_slides', function (Blueprint $table) {
                $table->string('secondary_cta_url', 500)
                    ->nullable()
                    ->after('secondary_cta_label')
                    ->comment('次按鈕連結');
            });
        }

        if (Schema::hasColumn('hero_slides', 'target_url')) {
            Schema::table('hero_slides', function (Blueprint $table) {
                $table->dropColumn('target_url');
            });
        }
    }
};



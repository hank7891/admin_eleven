<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 修復 migration baseline drift：
     * 專案從舊版 Laravel 升級至 Laravel 11，migrations 表保留舊版名稱，
     * 但 repo 已採用新版檔名，導致 php artisan migrate 嘗試重建已存在的表而報錯。
     */
    public function up(): void
    {
        # 移除 4 筆孤兒紀錄（舊版名稱，repo 中已無對應檔案）
        DB::table('migrations')->whereIn('migration', [
            '2014_10_12_000000_create_users_table',
            '2014_10_12_100000_create_password_resets_table',
            '2019_08_19_000000_create_failed_jobs_table',
            '2021_03_17_091850_create_employee_table',
        ])->delete();

        # 刪除舊版 password_resets 表（新版為 password_reset_tokens，schema 不同）
        Schema::dropIfExists('password_resets');

        # 刪除舊版 failed_jobs 表（由 create_jobs_table migration 以新版 schema 重建）
        Schema::dropIfExists('failed_jobs');

        # 建立 password_reset_tokens 表（原本由 create_users_table migration 建立，但 users 已存在無法直接執行）
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        # 建立 sessions 表（原本由 create_users_table migration 建立）
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        # 標記 create_users_table 為已執行（users 已存在，其餘表已在上方建立）
        $batch = DB::table('migrations')->max('batch') + 1;
        DB::table('migrations')->insert([
            'migration' => '0001_01_01_000000_create_users_table',
            'batch' => $batch,
        ]);
    }

    public function down(): void
    {
        # 移除插入的 create_users_table 記錄
        DB::table('migrations')->where('migration', '0001_01_01_000000_create_users_table')->delete();

        # 刪除本 migration 建立的表
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');

        # 重建舊版 password_resets 表
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        # 重建舊版 failed_jobs 表（無 uuid 欄位的舊版 schema）
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        # 還原 4 筆孤兒紀錄
        DB::table('migrations')->insert([
            ['migration' => '2014_10_12_000000_create_users_table', 'batch' => 1],
            ['migration' => '2014_10_12_100000_create_password_resets_table', 'batch' => 1],
            ['migration' => '2019_08_19_000000_create_failed_jobs_table', 'batch' => 1],
            ['migration' => '2021_03_17_091850_create_employee_table', 'batch' => 1],
        ]);
    }
};

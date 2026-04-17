<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    use HasFactory;

    /**
     * 資料表名稱
     * @var string
     */
    protected $table = 'about_page';

    /**
     * 可批量賦值欄位
     * @var array<int, string>
     */
    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_image_path',
        'story_title',
        'story_content',
        'mission_title',
        'mission_content',
        'vision_title',
        'vision_content',
        'contact_email',
        'contact_phone',
        'contact_address',
        'meta_description',
        'updated_by',
    ];

    /**
     * 型別轉換
     * @var array<string, string>
     */
    protected $casts = [
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 取得關於我們單例資料
     */
    public static function singleton(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'hero_title' => 'Aura & Heirloom',
                'hero_subtitle' => '為日常留一個慢下來的位置',
                'story_title' => '品牌故事',
                'story_content' => '請在後台編輯關於我們內容。',
            ]
        );
    }

    /**
     * 最後編輯者關聯
     */
    public function updater()
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }
}


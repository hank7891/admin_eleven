<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{

    /**
     * 資料表名稱
     * @var string
     */
    protected $table = 'hero_slides';

    /**
     * 可批量賦值欄位
     * @var array<int, string>
     */
    protected $fillable = [
        'image_path',
        'image_alt',
        'eyebrow',
        'title',
        'description',
        'target_url',
        'sort_order',
        'is_active',
        'start_at',
        'end_at',
        'created_by',
        'updated_by',
    ];

    /**
     * 型別轉換
     * @var array<string, string>
     */
    protected $casts = [
        'sort_order' => 'integer',
        'is_active'  => 'integer',
        'start_at'   => 'datetime',
        'end_at'     => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 啟用輪播
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', STATUS_ACTIVE);
    }

    /**
     * 生效中輪播
     */
    public function scopeInEffect(Builder $query): Builder
    {
        return $query
            ->where('start_at', '<=', now())
            ->where(function (Builder $builder) {
                $builder->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            });
    }

    /**
     * 依排序顯示
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    /**
     * 資料表名稱
     * @var string
     */
    protected $table = 'announcements';

    /**
     * 可批量賦值欄位
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'title',
        'summary',
        'content',
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
        'type'       => 'integer',
        'is_active'  => 'integer',
        'start_at'   => 'datetime',
        'end_at'     => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 建立者關聯
     */
    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    /**
     * 最後編輯者關聯
     */
    public function updater()
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }

    /**
     * 啟用公告
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', STATUS_ACTIVE);
    }

    /**
     * 全系統公告
     */
    public function scopeSystem(Builder $query): Builder
    {
        return $query->where('type', ANNOUNCEMENT_TYPE_SYSTEM);
    }

    /**
     * 一般公告
     */
    public function scopeGeneral(Builder $query): Builder
    {
        return $query->where('type', ANNOUNCEMENT_TYPE_GENERAL);
    }

    /**
     * 生效中公告
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
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    /**
     * 資料表名稱
     * @var string
     */
    protected $table = 'countries';

    /**
     * 可批量賦值欄位
     * @var array
     */
    protected $fillable = [
        'name',
        'abbreviation',
        'country_code',
        'is_active',
    ];

    /**
     * 型別轉換
     * @var array
     */
    protected $casts = [
        'is_active'  => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 啟用狀態顯示文字
     */
    public function getIsActiveDisplayAttribute(): string
    {
        return config('constants.status')[$this->is_active] ?? config('constants.status.' . STATUS_INACTIVE);
    }
}


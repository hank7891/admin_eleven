<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Employee extends Model
{
    use HasFactory;

    /**
     * 資料表名稱
     * @var string
     */
    protected $table = 'employee';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password'];

    /**
     * 不允許批量賦值的欄位
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 型別轉換
     * @var array
     */
    protected $casts = [
        'birthday' => 'date',
    ];

    /**
     * 性別對應文字
     */
    private const GENDER_MAP = [
        0 => '未指定',
        1 => '男',
        2 => '女',
    ];

    /**
     * 大頭照完整 URL
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    /**
     * 性別顯示文字
     */
    public function getGenderDisplayAttribute(): string
    {
        return self::GENDER_MAP[$this->gender] ?? '未指定';
    }
}

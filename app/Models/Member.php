<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Member extends Model
{
    /**
     * 資料表名稱
     * @var string
     */
    protected $table = 'member';

    /**
     * 可批量賦值欄位
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'phone',
        'birthday',
        'gender_key',
        'avatar_path',
        'status_key',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'registered_ip',
    ];

    /**
     * 隱藏欄位
     * @var array<int, string>
     */
    protected $hidden = ['password'];

    /**
     * 型別轉換
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 大頭照 URL
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return !empty($this->avatar_path) ? Storage::url($this->avatar_path) : null;
    }

    /**
     * 性別顯示文字
     */
    public function getGenderDisplayAttribute(): string
    {
        $key = (string) ($this->gender_key ?? GENDER_UNSPECIFIED);

        return config('constants.gender.' . $key)
            ?? config('constants.gender.' . GENDER_UNSPECIFIED)
            ?? '未指定';
    }

    /**
     * 會員狀態顯示文字
     */
    public function getStatusDisplayAttribute(): string
    {
        return config('constants.member_status.' . $this->status_key)
            ?? config('constants.member_status.inactive')
            ?? '停用';
    }
}


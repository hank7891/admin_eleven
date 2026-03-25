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
     * 多對多關聯角色
     */
    public function roles()
    {
        return $this->belongsToMany(AclRole::class, 'employee_acl_role', 'employee_id', 'acl_role_id');
    }

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
        return config('constants.gender')[$this->gender] ?? config('constants.gender.' . GENDER_UNSPECIFIED);
    }

    /**
     * 啟用狀態顯示文字
     */
    public function getIsActiveDisplayAttribute(): string
    {
        return config('constants.status')[$this->is_active] ?? config('constants.status.' . STATUS_INACTIVE);
    }
}

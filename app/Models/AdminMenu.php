<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminMenu extends Model
{
    use HasFactory;

    /**
     * 資料表名稱
     * @var string
     */
    protected $table = 'admin_menus';

    /**
     * 可批量賦值的屬性
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'name',
        'url',
        'icon',
        'sort_order',
        'is_active',
    ];

    /**
     * 型別轉換
     * @var array
     */
    protected $casts = [
        'parent_id'  => 'integer',
        'sort_order' => 'integer',
        'is_active'  => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 子選單
     */
    public function children()
    {
        return $this->hasMany(AdminMenu::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * 父層選單
     */
    public function parent()
    {
        return $this->belongsTo(AdminMenu::class, 'parent_id');
    }

    /**
     * 啟用狀態顯示文字
     */
    public function getIsActiveDisplayAttribute(): string
    {
        return config('constants.status')[$this->is_active] ?? '未知';
    }

    /**
     * 類型顯示文字
     */
    public function getTypeDisplayAttribute(): string
    {
        return $this->parent_id === 0 ? '群組' : '選單項目';
    }

    /**
     * 多對多關聯角色
     */
    public function roles()
    {
        return $this->belongsToMany(AclRole::class, 'acl_role_admin_menu');
    }
}

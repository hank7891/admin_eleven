<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use HasFactory;

    /**
     * 資料表名稱
     * @var string
     */
    protected $table = 'admin_logs';

    /**
     * 可批量賦值的屬性
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'operator_name',
        'ip_address',
        'module',
        'action',
        'target_id',
        'target_name',
        'changes',
        'remarks',
        'operated_at',
    ];

    /**
     * 應該被轉換成原生類型的屬性
     * @var array
     */
    protected $casts = [
        'changes' => 'json',
        'operated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 與操作者的關係
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * 獲取操作行為的顯示名稱
     */
    public function getActionDisplayAttribute()
    {
        $actions = config('admin_log.actions', []);
        return $actions[$this->action] ?? $this->action;
    }

    /**
     * 獲取模組的顯示名稱
     */
    public function getModuleDisplayAttribute()
    {
        $modules = config('admin_log.modules', []);
        return $modules[$this->module] ?? $this->module;
    }
}

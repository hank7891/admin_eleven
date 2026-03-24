<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLoginLog extends Model
{
    use HasFactory;

    /**
     * 資料表名稱
     * @var string
     */
    protected $table = 'admin_login_logs';

    /**
     * 可批量賦值的屬性
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'account',
        'employee_name',
        'action',
        'status',
        'fail_reason',
        'ip_address',
        'operated_at',
    ];

    /**
     * 型別轉換
     * @var array
     */
    protected $casts = [
        'operated_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * 關聯帳號
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * 操作類型顯示文字
     */
    public function getActionDisplayAttribute(): string
    {
        return config('constants.login_log_action')[$this->action] ?? $this->action;
    }

    /**
     * 狀態顯示文字
     */
    public function getStatusDisplayAttribute(): string
    {
        return config('constants.login_log_status')[$this->status] ?? '未知';
    }
}

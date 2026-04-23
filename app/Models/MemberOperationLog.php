<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberOperationLog extends Model
{
    use HasFactory;

    /**
     * 資料表名稱
     * @var string
     */
    protected $table = 'member_operation_logs';

    /**
     * 可批量賦值欄位
     * @var array<int, string>
     */
    protected $fillable = [
        'member_id',
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
     * 型別轉換
     * @var array<string, string>
     */
    protected $casts = [
        'changes' => 'json',
        'operated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 關聯會員
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}

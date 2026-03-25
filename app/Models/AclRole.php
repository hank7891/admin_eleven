<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AclRole extends Model
{
    use HasFactory;

    /**
     * 資料表名稱
     * @var string
     */
    protected $table = 'acl_role';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * 不允許批量賦值的欄位
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 多對多關聯帳號
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_acl_role', 'acl_role_id', 'employee_id');
    }
}

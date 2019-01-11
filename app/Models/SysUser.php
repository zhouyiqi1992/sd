<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysUser extends Model
{
    protected $table = 'sys_user';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'add_suid',
        'name',
        'account',
        'authstr',
        'update_suid',
        'created_at',
        'updated_at',
        'status'
    ];

    protected $hidden = [
        'authstr'
    ];

    /**
     * 管理员信息
     * @return mixed
     */
    public static function sysUserInfo()
    {
        return static::leftJoin('sys_user_role_map as m', 'm.suid', '=', 'sys_user.id')
            ->leftJoin('sys_role as r', 'r.id', '=', 'm.srid')
            ->orderBy('sys_user.created_at', 'desc')
            ->select(
                'sys_user.*',
                'r.name as role_name',
                'r.id as role_id'
            );
    }
}

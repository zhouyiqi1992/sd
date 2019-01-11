<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'sys_role';

    protected $fillable = [
        'name',
        'update_suid',
        'add_suid',
        'intro'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];
}

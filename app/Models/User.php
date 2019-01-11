<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'user';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'appid',
        'nickname',
        'sex',
        'location',
        'avator_url',
        'add_user_id',
        'update_user_id',
        'created_at',
        'updated_at'
    ];

}

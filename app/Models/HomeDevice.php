<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeDevice extends Model
{
    public $table = 'home_device';

    protected $fillable = [
        'name',
        'hid',
        'type',
        'add_user_id',
        'update_user_id',
        'created_at',
        'updated_at'
    ];


}

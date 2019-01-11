<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'Banner';

    protected $fillable = [
        'add_user_id',
        'update_user_id',
        'name',
        'pic_url',
        'status',
        'url_type',
        'url'
    ];
}

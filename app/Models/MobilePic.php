<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobilePic extends Model
{
    protected $table = 'mobile_pic';

    protected $fillable = [
        'id',
        'url'
    ];
}

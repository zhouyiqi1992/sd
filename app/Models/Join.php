<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Join extends Model
{
    protected $table = 'join_us';

    protected $fillable = [
        'id',
        'title',
        'content',
        'updated_at',
        'created_at'
    ];
}

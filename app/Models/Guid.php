<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guid extends Model
{
    protected $table = 'guid';

    protected $fillable = [
        'id',
        'name',
        'updated_at',
        'created_at'
    ];
}

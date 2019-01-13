<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pic extends Model
{
    public $table = 'product_img';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

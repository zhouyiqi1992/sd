<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';

    public function pic()
    {
        return $this->hasMany(Pic::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function setImgAttribute($img)
    {
        if (is_array($img)) {
            $this->attributes['img'] = json_encode($img);
        }
    }

    public function getImgAttribute($img)
    {
        return json_decode($img, true);
    }
}

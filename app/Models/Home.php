<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    public $table = 'home';

    protected $fillable = [
        'name',
        'province_code',
        'city_code',
        'area_code',
        'founding_date',
        'fee_min',
        'fee_max',
        'bedspace',
        'type',
        'address',
        'location_bd',
        'lng_bd',
        'lat_bd',
        'building_area',
        'building_type',
        'floors',
        'nurse_cnt',
        'is_medical_insurance',
        'medical_service',
        'assessment',
        'intro',
        'pay_style',
        'public_status',
        'add_user_id',
        'update_user_id',
        'created_at',
        'updated_at',
        'property',
        'age_min',
        'age_max',
        'url',
        'rate_star',
        'home_num',
        'phone'
    ];

    public function sqlHeader()
    {
        return $this->where('home.public_status', 1)
            ->select(
                'home.id',
                'home.lng_bd as longitude',
                'home.lat_bd as latitude')
            ->orderBy('home.id', 'desc');
    }
}

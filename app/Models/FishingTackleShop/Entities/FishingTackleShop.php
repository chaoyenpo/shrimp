<?php

namespace App\Models\FishingTackleShop\Entities;

use Illuminate\Database\Eloquent\Model;

class FishingTackleShop extends Model
{
    protected $table = 'fishing_tackle_shops';

    protected $fillable = [
        'name', 'address', 'location_lat', 'location_lng',
        'phone',
        'is_close'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_close' => 'boolean'
    ];

    public function businessHours()
    {
        return $this->hasMany('App\Models\System\BusinessHour', 'fishing_tackle_shop_id', 'id');
    }
}

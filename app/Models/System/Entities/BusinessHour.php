<?php

namespace App\Models\System\Entities;

use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    protected $table = 'business_hours';

    protected $fillable = [
        'shrimp_farm_id', 'fishing_tackle_shop_id',
        'day',
        'begin_at', 'end_at'
    ];

    public function shrimpFarm()
    {
        return $this->belongsTo('App\Models\ShrimpFarm\Entities\ShrimpFarm', 'shrimp_farm_id', 'id');
    }

    public function fishingTackleShop()
    {
        return $this->belongsTo('App\Models\FishingTackleShop\Entities\FishingTackleShop', 'fishing_tackle_shop_id', 'id');
    }
}

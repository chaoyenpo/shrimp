<?php

namespace App\Models\Ad\Entities;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $table = 'ads';

    protected $fillable = [
        'user_id',
        'category', 'name',
        'url', 'image_type', 'image', 'height',
        'weight',
        'location_lat', 'location_lng',
        'shopee', 'fb_group', 'fb_page', 'ig', 'youtube',
        'sales_farm', 'sales_shop',
        'is_enabled'
    ];
    protected $dates = [
        'begin_at',
        'end_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'sales_farm' => 'json',
        'sales_shop' => 'json',
        'is_enabled' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Profile\Entities\User', 'user_id', 'id');
    }

    public function scopeIsWithinMaxDistance($query, $lat, $lng, $radius) {
        $haversine = "(6371 * acos(cos(radians($lat)) 
                            * cos(radians(ads.location_lat)) 
                            * cos(radians(ads.location_lng) 
                            - radians($lng)) 
                            + sin(radians($lat)) 
                            * sin(radians(ads.location_lat))))";
        return $query->select('*')
                     ->selectRaw("{$haversine} AS distance");
                     //->whereRaw("{$haversine} < ?", [$radius]);
    }
}

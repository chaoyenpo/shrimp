<?php

namespace App\Models\Booth\Entities;

use Illuminate\Database\Eloquent\Model;

class Booth extends Model
{
    protected $table = 'booths';

    protected $fillable = [
        'user_id',
        'category', 'commodity', 'weight', 'price', 'status', 'note',
        'address', 'location_lat', 'location_lng',
        'begin_at', 'end_at',
        'is_enabled'
    ];
    protected $dates = [
        'begin_at',
        'end_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_enabled' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Profile\Entities\User', 'user_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Booth\Entities\BoothOrder', 'booth_id', 'id');
    }

    public function scopeIsWithinMaxDistance($query, $lat, $lng, $radius) {
        $haversine = "(6371 * acos(cos(radians($lat)) 
                            * cos(radians(booths.location_lat)) 
                            * cos(radians(booths.location_lng) 
                            - radians($lng)) 
                            + sin(radians($lat)) 
                            * sin(radians(booths.location_lat))))";
        return $query->select('*')
                     ->selectRaw("{$haversine} AS distance")
                     ->whereRaw("{$haversine} < ?", [$radius]);
    }
}

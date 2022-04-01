<?php

namespace App\Models\ShrimpFarm\Entities;

use Illuminate\Database\Eloquent\Model;

class ShrimpFarm extends Model
{
    protected $table = 'shrimp_farms';

    protected $fillable = [
        'name', 'address', 'location_lat', 'location_lng', 'phone',
        'content', 'news', 'can_push',
        'is_close'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'can_push' => 'boolean',
        'is_close' => 'boolean'
    ];

    public function events()
    {
        return $this->hasMany('App\Models\ShrimpFarm\Entities\ShrimpFarmEvent', 'shrimp_farm_id', 'id');
    }

    public function games()
    {
        return $this->hasMany('App\Models\Game\Entities\Game', 'shrimp_farm_id', 'id');
    }

    public function businessHours()
    {
        return $this->hasMany('App\Models\System\Entities\BusinessHour', 'shrimp_farm_id', 'id');
    }

    public function evaluations() {
        return $this->morphMany('App\Models\System\Entities\Evaluation', 'host')
                    ->orderBy('id', 'DESC');
    }

    public function userLiked()
    {
        return $this->hasOneThrough('App\Models\Profile\Entities\User',
                                    'App\Models\Profile\Entities\ProfileLikeFarm',
                                    'shrimp_farm_id',
                                    'id',
                                    'id',
                                    'user_id');
    }

    public function scopeIsWithinMaxDistance($query, $lat, $lng, $radius) {
        $haversine = "(6371 * acos(cos(radians($lat)) 
                            * cos(radians(shrimp_farms.location_lat)) 
                            * cos(radians(shrimp_farms.location_lng) 
                            - radians($lng)) 
                            + sin(radians($lat)) 
                            * sin(radians(shrimp_farms.location_lat))))";
        return $query->select('*')
                     ->selectRaw("{$haversine} AS distance");
                     //->whereRaw("{$haversine} < ?", [$radius]);
    }

    public function isLikedByUser($id)
    {
        return !!$this->userLiked()
                      ->where('users.id', '=', $id)
                      ->count();
    }
}

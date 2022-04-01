<?php

namespace App\Models\ShrimpFarm\Entities;

use Illuminate\Database\Eloquent\Model;

class ShrimpFarmEvent extends Model
{
    protected $table = 'shrimp_farms_events';

    protected $fillable = [
        'shrimp_farm_id',
        'content', 'images',
        'end_at'
    ];
    protected $dates = [
        'end_at',
        'created_at',
        'updated_at'
    ];

    public function setImagesAttribute($value)
    {
        return $this->attributes['images'] = json_encode($value, JSON_UNESCAPED_SLASHES);
    }

    public function getImagesAttribute()
    {
        return json_decode($this->attributes['images']);
    }

    public function shrimpFarm()
    {
        return $this->belongsTo('App\Models\ShrimpFarm\Entities\ShrimpFarm', 'shrimp_farm_id', 'id');
    }
}

<?php

namespace App\Models\Profile\Entities;

use Illuminate\Database\Eloquent\Model;

class ProfileLikeFarm extends Model
{
    protected $table = 'profile_like_farms';

    protected $fillable = [
        'user_id', 'shrimp_farm_id',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\Profile\Entities\User', 'user_id', 'id');
    }

    public function shrimpFarm()
    {
        return $this->belongsTo('App\Models\ShrimpFarm\Entities\ShrimpFarm', 'shrimp_farm_id', 'id');
    }
}

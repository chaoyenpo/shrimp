<?php

namespace App\Models\Recycler\Entities;

use Illuminate\Database\Eloquent\Model;

class Recycler extends Model
{
    protected $table = 'recycler_orders';

    protected $fillable = [
        'id',
        'recycler_name', 'recycler_id',
        'member_name', 'member_id','member_phone',
        'recycle_time', 'weight',
        'point', 'fee',
        'note'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [];
}

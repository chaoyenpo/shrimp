<?php

namespace App\Models\Transfer\Entities;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = 'transfer_point_records';

    protected $fillable = [
        'id',
        'giver_id', 'taker_id',
        'point', 'fee',
        'note',
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_confirmed'
    ];
}

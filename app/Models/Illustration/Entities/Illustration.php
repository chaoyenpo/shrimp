<?php

namespace App\Models\Illustration\Entities;

use Illuminate\Database\Eloquent\Model;

class Illustration extends Model
{
    protected $table = 'illustrations';

    protected $fillable = [
        'name', 'steps', 'lengths', 'data',
        'photo1', 'photo2', 'reviews', 'price', 'manufacturer', 'brand', 'youtube'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'data' => 'json'
    ];
}

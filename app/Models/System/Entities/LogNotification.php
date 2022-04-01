<?php

namespace App\Models\System\Entities;

use Illuminate\Database\Eloquent\Model;

class LogNotification extends Model
{
    protected $table = 'logs_notification';

    protected $fillable = [
        'title', 'message'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];
}

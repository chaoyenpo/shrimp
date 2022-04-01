<?php

namespace App\Models\System\Entities;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $table = 'evaluations';

    protected $fillable = [
        'user_id',
        'host_type', 'host_id',
        'score', 'description'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Profile\Entities\User', 'user_id', 'id');
    }

    /**
     * Get the owning commentable model.
     */
    public function host()
    {
        return $this->morphTo();
    }
}

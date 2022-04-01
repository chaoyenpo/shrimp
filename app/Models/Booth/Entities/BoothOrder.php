<?php

namespace App\Models\Booth\Entities;

use Illuminate\Database\Eloquent\Model;

class BoothOrder extends Model
{
    protected $table = 'booths_orders';

    protected $fillable = [
        'booth_id', 'customer_id',
        'customer_log', 'booth_log',
        'can_evaluate',
        'customer_evaluation_id', 'owner_evaluation_id',
        'is_close'
    ];

    protected $casts = [
        'can_evaluate' => 'boolean'
    ];

    public function booth()
    {
        return $this->belongsTo('App\Models\Booth\Entities\Booth', 'booth_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Profile\Entities\User', 'customer_id', 'id');
    }

    public function customer_evaluation()
    {
        return $this->belongsTo('App\Models\System\Entities\Evaluation', 'customer_evaluation_id', 'id');
    }

    public function owner_evaluation()
    {
        return $this->belongsTo('App\Models\System\Entities\Evaluation', 'owner_evaluation_id', 'id');
    }
}

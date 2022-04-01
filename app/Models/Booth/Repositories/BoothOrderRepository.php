<?php

namespace App\Models\Booth\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\Booth\Entities\BoothOrder;
use Carbon\Carbon;

class BoothOrderRepository extends EloquentRepository
{
    protected $model;

    public function __construct(BoothOrder $model)
    {
        $this->model = $model;
    }

    public function firstOrCreate($data)
    {
    	if ( !$this->model::where('booth_id', $data['booth_id'])
    	                  ->where('customer_id', $data['customer_id'])
    	                  ->where('is_close', 0)
    	                  ->first() ) {
    	   return $this->model::create($data);
    	}
    }

    public function seen($user_id)
    {
    	$list = [];
    	$orders = $this->model::where('is_close', 0)
    	                      ->where('customer_id', $user_id)
    	                      ->get();
    	foreach ($orders as $order) {
    		array_push($list, ['id'           => $order->id,
        	                   'can_evaluate' => $order->can_evaluate,
        	                   'log'          => $order->booth_log]);
    	}

    	return $list;
    }
}

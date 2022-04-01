<?php

namespace App\Models\System\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\System\Entities\PointRecord;

class PointRecordRepository extends EloquentRepository
{
    protected $model;

    public function __construct(PointRecord $model)
    {
        $this->model = $model;
    }

    public function getByOrderID($orderID)
    {
    	return $this->model->where('orderID', '=', $orderID)
    	                   ->orderBy('id', 'DESC')
    	                   ->first();
    }
}

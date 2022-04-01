<?php

namespace App\Models\FishingTackleShop\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\FishingTackleShop\Entities\FishingTackleShop;

class FishingTackleShopRepository extends EloquentRepository
{
    protected $model;

    public function __construct(FishingTackleShop $model)
    {
        $this->model = $model;
    }

    public function whereLocation($location_lat, $location_lng)
    {
        return $this->model->where('location_lat', $location_lat)
                           ->where('location_lng', $location_lng);
    }

    public function list($is_close = null)
    {
        $list = [];
        $records = $this->model::when(!is_null($is_close), function ($query) use ($is_close){
                                    	return $query->where('is_close', '=', $is_close);
                                    })
                               ->orderBy('updated_at', 'DESC')
                               ->get();

        foreach($records as $record){
            $list[] = ['id'         => $record->id,
                       'name'       => $record->name,
                       'address'    => $record->address,
                       'created_at' => $record->created_at->format('Y-m-d H:i:s'),
                       'updated_at' => $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : NULL];
        }

        return $list;
    }
}

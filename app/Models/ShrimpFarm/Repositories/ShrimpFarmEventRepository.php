<?php

namespace App\Models\ShrimpFarm\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\ShrimpFarm\Entities\ShrimpFarmEvent;
use Carbon\Carbon;

class ShrimpFarmEventRepository extends EloquentRepository
{
    protected $model;

    public function __construct(ShrimpFarmEvent $model)
    {
        $this->model = $model;
    }

    public function listForWeb()
    {
        $list = [];
        $records = $this->model::whereHas('shrimpFarm')
                               ->where(function($query){
                                   return $query->whereNull('end_at')
                                                ->orWhere('end_at', '>', Carbon::now()->format('Y-m-d H:i:s'));
                                })
                               ->orderBy('updated_at', 'DESC')
                               ->get();

        foreach($records as $record){
            $list[] = ['id'             => $record->id,
                       'shrimp_farm_id' => $record->shrimp_farm_id,
                       'name'           => $record->shrimpFarm->name,
                       'content'        => strip_tags($record->content),
                       'images'         => $record->images,
                       'end_at'         => $record->end_at ? $record->end_at->format('Y-m-d H:i:s') : NULL,
                       'created_at'     => $record->created_at ? $record->created_at->format('Y-m-d H:i:s') : NULL,
                       'updated_at'     => $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : NULL];
        }

        return $list;
    }
}

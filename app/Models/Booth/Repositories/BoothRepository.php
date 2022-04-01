<?php

namespace App\Models\Booth\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\Booth\Entities\Booth;
use Carbon\Carbon;

class BoothRepository extends EloquentRepository
{
    protected $model;

    public function __construct(Booth $model)
    {
        $this->model = $model;
    }

    public function list($user_id, $lat, $lng, $radius = 50)
    {
        $list = [];
        $records = $this->model::when($user_id, function ($query, $user_id){
                                        return $query->where('user_id', '=', $user_id);
                                    }, function ($query) use ($lat, $lng, $radius) {
                                    	return $query->where('is_enabled', '=', 1)
                                                     ->where(function($query){
					                                   return $query->whereNull('end_at')
					                                                ->orWhere('end_at', '>', Carbon::now()->format('Y-m-d H:i:s'));
					                                });
                                    })
                               ->isWithinMaxDistance($lat, $lng, $radius)
                               ->orderBy('distance', 'DESC')
                               ->get();

        foreach($records as $record){
            $data = ['id'           => $record->id,
                     'nickname'     => $record->user->nickname,
                     'phone'        => $record->user->phone,
                     'category'     => $record->category,
                     'commodity'    => $record->commodity,
                     'weight'       => $record->weight,
                     'price'        => $record->price,
                     'status'       => $record->status,
                     'note'         => $record->note,
                     'address'      => $record->address,
                     'location_lat' => $record->location_lat,
                     'location_lng' => $record->location_lng,
                     'distance'     => $user_id ? NULL : round($record->distance, 2) .'KM',
                     'begin_at'     => $record->begin_at->format('Y-m-d H:i:s'),
                     'end_at'       => $record->end_at ? $record->end_at->format('Y-m-d H:i:s') : NULL];
            if ($user_id)
                $data = array_merge($data, ['is_enabled' => $record->is_enabled]);
            array_push($list, $data);
        }

        return $list;
    }

    public function countSelfBooth($user_id, $except_id = null)
    {
        return $this->model::where('user_id', '=', $user_id)
                           ->when(!is_null($except_id), function ($query) use ($except_id){
                                        return $query->where('id', '<>', $except_id);
                                })
                           ->count();
    }
}

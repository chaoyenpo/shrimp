<?php

namespace App\Models\ShrimpFarm\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\ShrimpFarm\Entities\ShrimpFarm;
use Carbon\Carbon;

class ShrimpFarmRepository extends EloquentRepository
{
    protected $model;

    public function __construct(ShrimpFarm $model)
    {
        $this->model = $model;
    }

    public function whereLocation($location_lat, $location_lng)
    {
        return $this->model->where('location_lat', $location_lat)
                           ->where('location_lng', $location_lng);
    }

    public function hasOpen()
    {
        $list = [];
        $records = $this->model::whereHas('games')
                               ->orderBy('updated_at', 'DESC')
                               ->get();

        foreach($records as $record){
            $list[] = ['id'         => $record->id,
                       'name'       => $record->name,
                       'address'    => $record->address,
                       'is_close'   => $record->is_close,
                       'created_at' => $record->created_at ? $record->created_at->format('Y-m-d H:i:s') : NULL,
                       'updated_at' => $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : NULL];
        }

        return $list;
    }

    public function listForWeb($is_close = null)
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
                       'is_close'   => $record->is_close,
                       'created_at' => $record->created_at ? $record->created_at->format('Y-m-d H:i:s') : NULL,
                       'updated_at' => $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : NULL];
        }

        return $list;
    }

    public function listForMobile($type, $lat, $lng, $radius = 50)
    {
        $list = [];
        $records = $this->model::where('is_close', '=', 0)
                               ->isWithinMaxDistance($lat, $lng, $radius)
                               ->orderBy('distance', 'ASC')
                               ->take(500)
                               ->get();

        foreach($records as $record){
            if ($type == "event"){
            	$event = null;
                $events = $record->events->sortByDESC('id')->all();
                foreach ($events as $tmp) {
                	if (is_null($tmp->end_at) || $tmp->end_at > Carbon::now()) {
                	    $event = $tmp;
                	    break;
                	}
                }
                if (empty($event)) continue;
            } else {
                $event = $record->events->last();
                if (empty($event) || is_null($event->end_at) || $event->end_at < Carbon::now()) $event = null;
            }
            $list[] = ['id'            => $record->id,
                       'name'          => $record->name,
                       'score'         => $record->evaluations->count() == 0 ? null : $record->evaluations->sum('score') / $record->evaluations->count(),
                       'address'       => $record->address,
                       'location_lat'  => $record->location_lat,
                       'location_lng'  => $record->location_lng,
                       'distance'      => round($record->distance, 2) .'KM',
                       'event_content' => empty($event) ? null : $event->content];
        }

        return $list;
    }
}

<?php

namespace App\Models\Game\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\Game\Entities\Game;

class GameRepository extends EloquentRepository
{
    protected $model;

    public function __construct(Game $model)
    {
        $this->model = $model;
    }

    public function listForBackend()
    {
        $list = [];
        $records = $this->model::orderBy('begin_at', 'DESC')
                               ->get();

        foreach($records as $record){
            $data = [
                'id'                => $record->id,
                'shrimp_farm_id'    => $record->shrimp_farm_id,
                'identifier'        => $record->identifier,
                'name'              => $record->name,
                'location_catrgory' => $record->location_catrgory,
                'people_num'        => $record->people_num,
                'host_quota'        => $record->host_quota,
                'note'              => $record->note,
                'community'         => $record->community,
                'sponsor'           => $record->sponsor,
                'bait'              => $record->bait,
                'status'            => $record->status,
                'mode'            => $record->mode,
                'start_at'      => $record->start_at,
                'statusText'        => $record->statusText(),
                'begin_at'          => $record->begin_at ? $record->begin_at->format('Y-m-d') : NULL,
                'created_at'        => $record->created_at->format('Y-m-d H:i:s'),
                'updated_at'        => $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : NULL];
            array_push($list, $data);
        }

        return $list;
    }

    public function listForFrontend($status, $query = [])
    {
        $list = [];
        if ($status == 'end') {
            $records = $this->model::where('status', 'end')
                                   ->where('status', '<>', 'cancel')
                                   ->where($query)
                                   ->orderBy('begin_at', 'DESC')
                                   ->get();
        } elseif ($status == 'notend') {
            $records = $this->model::where('status', '<>', 'end')
                                   ->where('status', '<>', 'cancel')
                                   ->where($query)
                                   ->orderBy('begin_at', 'ASC')
                                   ->get();
        }

        foreach($records as $record){
            $statusText = $record->statusText();
            if ($record->status == 'create') {
                $statusText = '預計於 '.$record->startAtWithWeek().' 開放報名';
            }

            $host_main_personnel = $record->members(['host_main_personnel'])->get();
            $game_member = $record->members(['ok','waiting','pending'])->get();
            array_push($list, [
                'id'             => $record->id,
                'sponsor'        => $record->sponsor,
                'name'           => $record->name,
                'identifier'     => $record->identifier,
                'begin_at'       => $record->beginAtWithWeek(),
                'shrimp_farm_id' => $record->shrimp_farm_id,
                'shrimp_farm'    => $record->shrimpFarm->name,
                'people_num'     => $record->people_num,
                'community'      => $record->community,
                'start_at'      => $record->start_at,
                'location_catrgory' => $record->location_catrgory,
                'people_now'     => $record->members(['ok','waiting','host_quota', 'pending'])->count(),
                'host_main_personnel_ids' => $host_main_personnel->pluck('user_id')->toArray(),
                'host_main_personnel' => $host_main_personnel,
                'game_member_ids' => $game_member->pluck('user_id')->toArray(),
                'statusText'     => $statusText
            ]);
        }

        return $list;
    }
}
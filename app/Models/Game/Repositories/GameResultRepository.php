<?php

namespace App\Models\Game\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\Game\Entities\GameResult;

class GameResultRepository extends EloquentRepository
{
    protected $model;

    public function __construct(GameResult $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        $list = [];
        $records = $this->model::orderBy('updated_at', 'DESC')
                               ->get();

        foreach($records as $record){
            $data = [
                'id'          => $record->id,
                'user_id'     => $record->user_id,
                'game_id'     => $record->game_id,
                'level'       => $record->level,
                'number'      => $record->number,
                'numberText'  => $record->numberText(),
                'point'       => $record->point,
                'result'      => $record->result,
                'resultText'  => $record->resultText(),
                'is_pk_win'   => $record->is_pk_win,
                'can_edit'    => $record->can_edit,
                'integral'    => $record->integral,
                'created_at'  => $record->created_at->format('Y-m-d H:i:s'),
                'updated_at'  => $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : NULL];
            array_push($list, $data);
        }

        return $list;
    }
}

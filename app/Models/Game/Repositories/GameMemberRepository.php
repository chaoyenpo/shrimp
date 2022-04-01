<?php

namespace App\Models\Game\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\Game\Entities\Game;
use App\Models\Game\Entities\GameMember;
use Carbon\Carbon;

class GameMemberRepository extends EloquentRepository
{
    protected $model;

    public function __construct(GameMember $model)
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
                'status'      => $record->status,
                'register_at' => $record->register_at ? $record->register_at->format('Y-m-d H:i:s') : NULL,
                'is_check_in' => $record->is_check_in,
                'created_at'  => $record->created_at->format('Y-m-d H:i:s'),
                'updated_at'  => $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : NULL];
            array_push($list, $data);
        }

        return $list;
    }

    public function updatePersonnel(Int $user_id, Int $game_id, $status)
    {
        $this->model::where('user_id', $user_id)
                    ->where('game_id', $game_id)
                    ->delete();
        $this->model::where('game_id', $game_id)
                    ->where('status', 'host_main_personnel')
                    ->delete();

        return $this->model::create([
            'user_id' => $user_id,
            'game_id' => $game_id,
            'status'  => $status
        ]);
    }

    public function checkHost(Int $user_id, Int $game_id)
    {
        return $this->model::where('user_id', $user_id)
                           ->where('game_id', $game_id)
                           ->whereIn('status', ['host_main_personnel', 'host_personnel'])
                           ->exists();
    }

    public function updateHostquota(Int $user_id, Int $game_id)
    {
        $this->model::where('user_id', $user_id)
                    ->where('game_id', $game_id)
                    ->delete();

        return $this->model::create([
            'user_id'     => $user_id,
            'game_id'     => $game_id,
            'status'      => 'host_quota',
            'register_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function canNotSignup(Int $user_id, Int $game_id)
    {
        $game = Game::find($game_id);
        $date = $game->begin_at->format('Y-m-d');

        return $this->model::where('user_id', $user_id)
                           ->when($game_id, function ($query, $game_id) {
                                return $query->where('game_id', '<>', $game_id);
                            })
                           ->whereHas('game', function($query) use ($date) {
                                $query->whereDate('begin_at', Carbon::parse($date));
                            })
                           ->exists();
    }
}

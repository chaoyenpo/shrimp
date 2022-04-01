<?php

namespace App\Models\Profile\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use App\Models\Game\Entities\GameResult;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'api_token', 'device_token', 'imei', 'firebase_uid',
        'nickname', 'nickname_count', 'photo', 'note', 'email', 'phone',
        'location_lat', 'location_lng',
        'is_vendor', 'is_shrimper', 'is_recycler',
        'point', 'sale_count', 'buy_count',
        'is_login', 'can_push_booth_1', 'can_push_booth_2', 'can_push_shrimp_event',
        'login_at'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'is_vendor'            => 'boolean',
        'is_shrimper'          => 'boolean',
        'is_login'             => 'boolean',
        'can_receive_messages' => 'boolean'
    ];

    public function nicknameWithPhone()
    {
        if (empty($this->phone))
            return $this->nickname;

        return $this->nickname .'（'. substr($this->phone, -4) .'）';
    }

    public function nicknameWithFullPhone()
    {
        if (empty($this->phone))
            return $this->nickname;

        return $this->nickname .'（'. $this->phone .'）';
    }

    public function pointRecords($is_confirmed = null)
    {
        return $this->hasMany('App\Models\System\Entities\PointRecord', 'user_id', 'id')
                    ->unless(is_null($is_confirmed), function ($query) use ($is_confirmed) {
                            return $query->where('is_confirmed', $is_confirmed);
                        })
                    ->orderBy('updated_at', 'DESC');
    }

    public function likeFarms()
    {
        return $this->hasManyThrough('App\Models\ShrimpFarm\Entities\ShrimpFarm',
                                     'App\Models\Profile\Entities\ProfileLikeFarm',
                                     'user_id',
                                     'id',
                                     'id',
                                     'shrimp_farm_id',
                                     'user_id');
    }

    public function gameMembers($status = null)
    {
        return $this->hasMany('App\Models\Game\Entities\GameMember', 'user_id', 'id')
                    ->when($status, function ($query, $status) {
                            return $query->whereHas('game', function($query) use ($status) {
                                $query->where('status', $status);
                            });
                        }, function ($query) {
                            return $query->whereHas('game', function($query) {
                                $query->where('status', '<>', 'cancel')
                                      ->where('status', '<>', 'end');
                            });
                        })
                    ->orderBy('created_at', 'DESC');
    }

    public function gameResults($game_id = null)
    {
        return $this->hasMany('App\Models\Game\Entities\GameResult', 'user_id', 'id')
                    ->where('level', 'final')
                    ->when($game_id, function ($query, $game_id) {
                            return $query->where('game_id', $game_id);
                        })
                    // ->whereHas('game', function($query) {
                    //     $query->where('status', '=', 'end');
                    // })
                    ->orderBy('created_at', 'DESC');
    }

    public function scopeIsWithinMaxDistance($query, $lat, $lng, $radius, $data) {
        $haversine = "(6371 * acos(cos(radians($lat)) 
                            * cos(radians(users.location_lat)) 
                            * cos(radians(users.location_lng) 
                            - radians($lng)) 
                            + sin(radians($lat)) 
                            * sin(radians(users.location_lat))))";
        return $query->select('*')
                     ->selectRaw("{$haversine} AS distance")
                     ->when(isset($data['can_push_booth_1']), function ($query) use ($data) {
                            return $query->where('can_push_booth_1', $data['can_push_booth_1']);
                        })
                     ->when(isset($data['can_push_booth_2']), function ($query) use ($data) {
                            return $query->where('can_push_booth_2', $data['can_push_booth_2']);
                        })
                     ->when(isset($data['can_push_shrimp_event']), function ($query) use ($data) {
                            return $query->where('can_push_shrimp_event', $data['can_push_shrimp_event']);
                        })
                     ->whereRaw("{$haversine} < ?", [$radius]);
    }

    public function gameChampionCount($nums = null)
    {
        return $this->hasMany('App\Models\Game\Entities\GameResult', 'user_id', 'id')
                    ->where('level', 'final')
                    ->where('result', 1)
                    ->when($nums, function ($query, $nums) {
                            return $query->whereDate('updated_at', '>=', Carbon::today()->subWeeks($nums));
                        })
                    ->count();
    }

    public function gamePKCount($nums = null)
    {
        $round1 = $this->hasMany('App\Models\Game\Entities\GameResult', 'user_id', 'id')
                    ->where('result', 'like', '%PK%')
                    ->where('level', 'round1')
                    ->get()
                    ->pluck('game_id')
                    ->toArray();

        $finals = $this->hasMany('App\Models\Game\Entities\GameResult', 'user_id', 'id')
                    ->where('level', 'final')
                    ->get();

        $final_count = 0;
        foreach ($finals as $final) {
            if (in_array($final->game_id, $round1)) {
                continue;
            }

            $result = GameResult::where('game_id', $final->game_id)
            ->where('level', 'final')
            ->where('user_id', '!=', $final->user_id)
            ->where('point', $final->point)
            ->where('result', '<=', 5)
            ->first();

            if ($result) {
                $final_count++;
            }
        }

        // if ($this->id == 6978) {
        //     dd($result);            
        // }

        return $final_count + count($round1);
    }

    public function gameJoinCount($nums = null)
    {
        return $this->hasMany('App\Models\Game\Entities\GameResult', 'user_id', 'id')
                    ->where('level', 'round1')
                    ->count();
    }

    public function gamePreChampionCount($nums = null)
    {
        $user_id = $this->id;
        return $this->hasMany('App\Models\Game\Entities\GameResult', 'user_id', 'id')
                    ->where(function($query) use ($user_id) {
                        $query->where('level', 'round1')
                        ->where('user_id', $user_id)
                        ->where('result', 1);
                    })
                    ->orWhere(function($query) use ($user_id) {
                        $query->where('level', 'round1')
                        ->where('is_pk_win', 1)
                        ->where('user_id', $user_id)
                        ->where('result', '冠軍PK');
                    })
                    ->count();
    }

    public function gamePoint($nums = null)
    {
        return $this->hasMany('App\Models\Game\Entities\GameResult', 'user_id', 'id')
                    ->when($nums, function ($query, $nums) {
                            return $query->whereDate('updated_at', '>=', Carbon::today()->subWeeks($nums));
                        })
                    ->sum('point');
    }

    public function gameIntegral($nums = null, $game_id = null)
    {
        return $this->hasMany('App\Models\Game\Entities\GameResult', 'user_id', 'id')
                    ->when($nums, function ($query, $nums) {
                            return $query->whereDate('updated_at', '>=', Carbon::today()->subWeeks($nums));
                        })
                    ->when($game_id, function ($query, $game_id) {
                            return $query->where('game_id', $game_id);
                        })                    
                    ->sum('integral');
    }
}

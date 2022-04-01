<?php

namespace App\Models\Game\Entities;

use Illuminate\Database\Eloquent\Model;

class GameMember extends Model
{
    protected $table = 'games_members';

    protected $fillable = [
        'user_id', 'game_id',
        'status', 'register_at', 'is_check_in'
    ];
    protected $dates = [
        'register_at',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'is_check_in' => 'boolean'
    ];

    public function statusText()
    {
        switch ($this->status)
        {
            case "ok":                  return "報名成功"; break;
            case "waiting":             return "候補"; break;
            case "fail":                return "報名失敗"; break;
            case "host_quota":          return "主辦方名額"; break;
            case "host_main_personnel": return "主要工作人員"; break;
            case "host_personnel":      return "工作人員"; break;
        }
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Profile\Entities\User', 'user_id', 'id');
    }

    public function game()
    {
        return $this->belongsTo('App\Models\Game\Entities\Game', 'game_id', 'id');
    }

    public function results()
    {
        return $this->hasMany('App\Models\Game\Entities\GameResult', 'user_id', 'user_id')
                    ->where('game_id', $this->game_id);
    }
}

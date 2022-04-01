<?php

namespace App\Models\Game\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\Game\Entities\GameMember;

class GameResult extends Model
{
    protected $table = 'games_results';

    protected $fillable = [
        'user_id', 'game_id',
        'level', 'number', 'point',
        'result', 'is_pk_win', 'can_edit', 'can_random', 'integral'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'is_pk_win'  => 'boolean',
        'can_edit'   => 'boolean',
        'can_random' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Profile\Entities\User', 'user_id', 'id');
    }

    public function game()
    {
        return $this->belongsTo('App\Models\Game\Entities\Game', 'game_id', 'id')
                    ->withTrashed();
    }

    public function member()
    {
        return GameMember::where('game_id', $this->game_id)
                         ->where('user_id', $this->user_id)
                         ->first();
    }

    public function numberText()
    {
        return ($this->number > $this->game->people_num/2) ? $this->number-$this->game->people_num/2 : $this->number;
    }

    public function sumIntegral()
    {
        return $this->where('user_id', $this->user_id)->where('game_id', $this->game_id)->sum('integral');
    }

    public function resultText($level = null)
    {
        $pre_champion_ids = [];
        foreach ([[1,$this->game->people_num/2], [$this->game->people_num/2+1,$this->game->people_num]] as $number) {
            $pre_champion = $this->game->results('round1', $number)
            ->where(function ($query)
             {
                  $query->where('is_pk_win', 1)
                        ->where('result', '冠軍PK')
                        ->orWhere('result', 1);
             })
            ->first();
            $pre_champion_ids[] = $pre_champion['user_id'];
        }

        $bet_result = $this->game->results()->select('user_id', \DB::raw('SUM(point) as sum_point'))
        ->orderBy('sum_point', 'DESC')
        ->groupBy('user_id')
        ->first();

        if (is_null($level)) {
            if (in_array($this->result, ['晉級PK','冠軍PK'])) {
                if ($this->is_pk_win)
                    return $this->result.'勝出';
                else
                    return $this->result;
            } elseif ($this->result == 1) {
                return '冠軍';
            } else {
                return "-";
            }
        } elseif ($level == 'final') {
            $text = '';

            if (strstr($this->result, 'PK')) {
                $result = explode('-', $this->result);
                if ($result[0] == 1)
                    $text = '冠軍 PK';
                elseif ($result[0] == 2)
                    $text = '亞軍 PK';
                elseif ($result[0] == 3)
                    $text = '季軍 PK';
                elseif ($result[0] == 4)
                    $text = '殿軍 PK';
                elseif ($this->result == 5)
                    $text = '第 5 名';
            } else {
                if ($this->result == 1)
                    $text = '冠軍';
                elseif ($this->result == 2)
                    $text = '亞軍';
                elseif ($this->result == 3)
                    $text = '季軍';
                elseif ($this->result == 4)
                    $text = '殿軍';
                elseif ($this->result == 5)
                    $text = '第 5 名';
                    //'第 '.$this->result.' 名';
            }

            // if ($this->integral > 0 && $bet_result['user_id'] == $this->user_id) {
            //     if (!empty($text)) {
            //         $text .= '、';
            //     }
            //     $text .= 'MVP';
            // }

            if ($this->integral > 0 && in_array($this->user_id, $pre_champion_ids)) {
                if (!empty($text)) {
                    $text .= '、';
                }
                $text .= '預冠';
            }

            return $text ?? '-';
        }
    }

    public function canAdvance()
    {
        if ($this->result == '冠軍PK')
            return true;

        $this->result = (int) $this->result;
        return ($this->is_pk_win || $this->result > 0) ? true : false;
    }
}

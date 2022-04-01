<?php

namespace App\Models\Game\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class FullTwGame extends Model
{
    use SoftDeletes;

    protected $table = 'full_tw_game';

    protected $fillable = [
        'shrimp_farm_id',
        'name',
        'people_num',
        'mode',
        'vendor',
        'status',
        'begin_at',
    ];

    protected $hidden = [
        'deleted_at'
    ];
    protected $dates = [
        'begin_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'progress' => 'array'
    ];

    public function statusText()
    {
        switch ($this->status)
        {
            case "cancel":  return "比賽已取消"; break;
            case "create":  return "建立比賽"; break;
            case "sign_up": return "開放報名"; break;
            case "prepare": return "賽前整備"; break;
            case "ing":     return "比賽進行中"; break;
            case "end":     return "比賽結束"; break;
        }
    }
    public function modeText()
    {
        switch ($this->mode)
        {
            case "single": return "個人"; break;
            case "three": return "三人團體"; break;
            case "four": return "四人團體"; break;
            case "six": return "六人團體"; break;
            case "eight": return "八人團體"; break;
        }
    }

    public function signupAt()
    {
        if ($this->begin_at == null)
            return null;
        return Carbon::parse($this->begin_at->subDays(42)->format('Y-m-d') . " 21:00");
    }

    public function signupAtWithWeek()
    {
        if ($this->begin_at == null)
            return null;
        return $this->signupAt() . '（' .$this->dayOfWeekText('signup_at'). '）';
    }

    public function beginAtWithWeek()
    {
        if ($this->begin_at == null)
            return null;
        return $this->begin_at . '（' .$this->dayOfWeekText('begin_at'). '）';
    }

    public function dayOfWeekText($type)
    {
        if ($this->begin_at == null)
            return null;
        if ($type == 'signup_at')
            $target = $this->signupAt()->dayOfWeek;
        else
            $target = $this->begin_at->dayOfWeek;

        switch ($target)
        {
            case "0": return "日"; break;
            case "1": return "一"; break;
            case "2": return "二"; break;
            case "3": return "三"; break;
            case "4": return "四"; break;
            case "5": return "五"; break;
            case "6": return "六"; break;
        }
    }

    public function shrimpFarm()
    {
        return $this->belongsTo('App\Models\ShrimpFarm\Entities\ShrimpFarm', 'shrimp_farm_id', 'id');
    }

    public function members($status = null, $user_id = null)
    {
        return $this->hasMany('App\Models\Game\Entities\GameMember', 'game_id', 'id')
                    ->when($status, function ($query, $status) {
                                if (is_string($status))
                                    return $query->where('status', $status);
                                elseif (is_array($status))
                                    return $query->whereIn('status', $status);
                            })
                    ->when($user_id, function ($query, $user_id) {
                                return $query->where('user_id', $user_id);
                            });
    }

    public function enters(String $level, Array $numbers, Int $threshold)
    {
        $records = $this->hasMany('App\Models\Game\Entities\GameResult', 'game_id', 'id')
                        ->where('level', $level)
                        ->where('number', '>=', $numbers[0])
                        ->where('number', '<=', $numbers[1])
                        ->get();
        $count = 0;
        foreach ($records as $record) {
            if (is_numeric($record->result) || $record->result == '冠軍PK')
                $count++;
        }

        return $count;
    }

    public function results($level = null, $number = null, $point = -1, $result = null, $is_pk = null)
    {
        return $this->hasMany('App\Models\Game\Entities\GameResult', 'game_id', 'id')
                    ->when($level, function ($query, $level) {
                                return $query->where('level', $level);
                            })
                    ->when($number, function ($query, $number) {
                                if (is_integer($number))
                                    return $query->where('number', $number);
                                elseif (is_array($number))
                                    return $query->where('number', '>=', $number[0])
                                                 ->where('number', '<=', $number[1]);
                            })
                    ->unless($point == -1, function ($query) use ($point) {
                                if (is_null($point))
                                    return $query->whereNull('point');
                                else
                                    return $query->where('point', $point);
                            })
                    ->when($result, function ($query, $result) {
                                return $query->where('result', $result);
                            })
                    ->unless(is_null($is_pk), function ($query, $is_pk) {
                                return $query->where('is_pk', $is_pk);
                            });
    }
}

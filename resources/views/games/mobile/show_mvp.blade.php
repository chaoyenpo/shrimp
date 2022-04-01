@php
    $request = \Illuminate\Support\Facades\Request::instance();
    $user_id = $request->get('user_id');
    $bet_result = $game->results()->select('user_id', \DB::raw('SUM(point) as sum_point'))
    ->orderBy('sum_point', 'DESC')
    ->groupBy('user_id')
    ->get();

    $mvps = [];
    foreach ($bet_result as $result) {
        if (empty($mvps)) {
            $mvps = [$result];
        } else {
            if ($mvps[0]->sum_point == $result->sum_point) {
                $mvps[] = $result;
            }
        }
    }

    $pre_champions = [];
    foreach ([[1,$game->people_num/2], [$game->people_num/2+1,$game->people_num]] as $number) {
        $pre_champion = $game->results('round1', $number)
        ->where(function ($query)
         {
              $query->where('is_pk_win', 1)
                    ->where('result', '冠軍PK')
                    ->orWhere('result', 1);
         })
        ->first();
        $pre_champions[] = $pre_champion;
    }

@endphp


<div class="table-responsive">
<h4>比賽結果</h4>

@foreach ($game->results('final', [1,$game->people_num/2])->where('result', '<=', 5)->orderBy('result', 'asc')->get() as $result)
<h6>{{$result->resultText('final')}}: {{$result->user->nicknameWithPhone()}}</h6>
@endforeach

<h6>MVP: 
    @foreach ($mvps as $key => $mvp)
    {{$mvp->user->nicknameWithPhone()}}@if (count($mvps) - 1 != $key)、@endif
    @endforeach
</h6>

@foreach($pre_champions as $pre_champion)
<h6>預賽冠軍: {{$pre_champion->user->nicknameWithPhone()}}</h6>
@endforeach
<br>
<h6>總斬蝦數: {{$game->results()->sum('point')}}</h6>
</div>
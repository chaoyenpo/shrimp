@extends('activities.index')

@section('content')

@php
    $request = \Illuminate\Support\Facades\Request::instance();
    $api_token = $request->get('api_token');
    $imei = $request->get('imei');
    $user = \App\Models\Profile\Entities\User
                ::where('api_token', $api_token)
                ->where('imei', $imei)
                ->first();

    $list = collect($records)->sortByDesc(function ($item, $key) {
        return $item['integral'];
    });
//$user = \App\Models\Profile\Entities\User::find(973);
@endphp

<h2>{{ $title }}</h2>

<div style="margin: 0 10px;">
    @forelse($records as $record)
        @php
        $key = $rank_list[$record['id']] ?? 99;

        $image = '';
        if ($key == 0) {
            $image = '閃電黑';
        } else if ($key >= 1 && $key <= 12) {
            $image = '閃電紅';
        } else if ($key >= 13 && $key <= 29) {
            $image = '閃電紫';
        }

        @endphp
        <hr size="1px" align="center" width="100%">
        @if ($record['id'] == $user['id'])
        <div class="row align-items-center bg-warning">
        @else    
        <div class="row align-items-center" style="background-image: url('/{{$image}}.gif')">
        @endif
            <div class="rank">
                @if ($type == 'integral')
                    @if ($record['integral'] == 0)
                        <h1>--</h1>
                    @else
                        @switch($record['rank'])
                            @case(1)
                                <img src="{{ asset('rank1.png') }}" alt="">
                                @break
                            @case(2)
                                <img src="{{ asset('rank2.png') }}" alt="">
                                @break
                            @case(3)
                                <img src="{{ asset('rank3.png') }}" alt="">
                             @break
                            @default
                                <h1>{{ $record['rank'] }}</h1>
                            @break
                        @endswitch
                    @endif
                @elseif ($type == 'point')
                    @if ($record['point'] == 0)
                        <h1>--</h1>
                    @else
                    @switch($record['rank'])
                            @case(1)
                                <img src="{{ asset('rank1.png') }}" alt="">
                                @break
                            @case(2)
                                <img src="{{ asset('rank2.png') }}" alt="">
                                @break
                            @case(3)
                                <img src="{{ asset('rank3.png') }}" alt="">
                             @break
                            @default
                                <h1>{{ $record['rank'] }}</h1>
                            @break
                        @endswitch
                    @endif
                @elseif ($type == 'pkking')
                    @if ($record['pkCount'] == 0)
                        <h1>--</h1>
                    @else
                    @switch($record['rank'])
                            @case(1)
                                <img src="{{ asset('rank1.png') }}" alt="">
                                @break
                            @case(2)
                                <img src="{{ asset('rank2.png') }}" alt="">
                                @break
                            @case(3)
                                <img src="{{ asset('rank3.png') }}" alt="">
                             @break
                            @default
                                <h1>{{ $record['rank'] }}</h1>
                            @break
                        @endswitch
                    @endif
                @elseif ($type == 'hotking')
                    @if ($record['joinCount'] == 0)
                        <h1>--</h1>
                    @else
                    @switch($record['rank'])
                            @case(1)
                                <img src="{{ asset('rank1.png') }}" alt="">
                                @break
                            @case(2)
                                <img src="{{ asset('rank2.png') }}" alt="">
                                @break
                            @case(3)
                                <img src="{{ asset('rank3.png') }}" alt="">
                             @break
                            @default
                                <h1>{{ $record['rank'] }}</h1>
                            @break
                        @endswitch
                    @endif
                @elseif ($type == 'champion')
                    @if ($record['championCount'] == 0)
                        <h1>--</h1>
                    @else
                    @switch($record['rank'])
                            @case(1)
                                <img src="{{ asset('rank1.png') }}" alt="">
                                @break
                            @case(2)
                                <img src="{{ asset('rank2.png') }}" alt="">
                                @break
                            @case(3)
                                <img src="{{ asset('rank3.png') }}" alt="">
                             @break
                            @default
                                <h1>{{ $record['rank'] }}</h1>
                            @break
                        @endswitch
                    @endif
                @elseif ($type == 'preChampion')
                    @if ($record['preChampionCount'] == 0)
                        <h1>--</h1>
                    @else
                    @switch($record['rank'])
                            @case(1)
                                <img src="{{ asset('rank1.png') }}" alt="">
                                @break
                            @case(2)
                                <img src="{{ asset('rank2.png') }}" alt="">
                                @break
                            @case(3)
                                <img src="{{ asset('rank3.png') }}" alt="">
                             @break
                            @default
                                <h1>{{ $record['rank'] }}</h1>
                            @break
                        @endswitch
                    @endif
                @endif
            </div>
            <div class="col text-center" id="photo_wrap">
                <img src="{{ $record['photo'] }}"  class="rounded-circle avatar">
            </div>
            <div class="col">
                @if ($user)
                    <a href="{{ url('api/game/profile') }}/{{ $record['id'] }}?api_token={{ $api_token }}&imei={{ $imei }}">{{ $record['nickname'] }}</a>
                @else
                    <a href="{{ url('api/game/profile') }}/{{ $record['id'] }}">{{ $record['nickname'] }}</a>
                @endif
                <div>
                    <span>{{ $record['note'] }}</span>
                </div>
            </div>
            <div class="col">
                @if ($type == 'integral')
                    <h1>{{ $record['integral'] }}分</h1>
                @elseif ($type == 'point')
                    <h1>{{ $record['point'] }}斬</h1>
                @elseif ($type == 'hotking')
                    <h1>{{ $record['joinCount'] }}場次</h1>
                @elseif ($type == 'pkking')
                    <h1>{{ $record['pkCount'] }}次</h1>
                @elseif ($type == 'champion')
                    <h1>{{ $record['championCount'] }}勝</h1>
                @elseif ($type == 'preChampion')
                    <h1>{{ $record['preChampionCount'] }}次</h1>
                @endif
            </div>
        </div>
    @empty
        <hr size="1px" align="center" width="100%">
        <div>
            目前尚無紀錄
        </div>
    @endforelse
</div>

<style>
#photo_wrap {
    max-height: 200px;
    max-width: 200px;
}
#photo_wrap img {
    max-height: 100%;
    max-width: 100%;
}
@media (min-width: 768px) {
    #photo_wrap {
        max-height: 400px;
        max-width: 400px;
    }
}
</style>

@endsection

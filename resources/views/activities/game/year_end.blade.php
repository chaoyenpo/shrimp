@extends('activities.index')

@section('content')

<h2>年終大獎賽</h2>

<div class="row">
    <div class="col-md-12 my-3 px-4">
        @forelse($games as $game)
            <hr size="1px" align="center" width="100%">
            <p>編號：<a href="{{ url('/api/game') }}/{{ $game['identifier'] }}">{{ $game['identifier'] }}</a></p>
            <p>日期：{{ $game['begin_at'] }}</p>
            <p>場地：{{ $game['shrimp_farm'] }}</p>
            <p>報名：{{ $game['people_now'] }} / {{ $game['people_num'] }}</p>
            <p>狀態：{{ $game['statusText'] }}</p>
        @empty
            <p>目前無任何比賽</p>
        @endforelse
    </div>
</div>

@endsection

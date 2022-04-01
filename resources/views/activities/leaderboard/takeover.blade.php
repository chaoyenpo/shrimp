@extends('activities.index')

@section('content')

<h2>制霸成就</h2>

<div style="margin: 0 10px;">
    <div style="border:2px solid #f1f52c;display:flex; flex-direction:row;">
        <img style="width:30%;" src="{{ $user['photo'] }}">
        <div style="margin-left:10px;">
            <p>暱稱:{{ $user['nickname'] }}</p>
            <p>稱號:{{ $user['title'] }}</p>
            <p>積分:{{ $user['point'] }}</p>
            <p>排名:{{ $user['ranking'] }}</p>
        </div>            
    </div>
    <div style="display:flex; flex-direction:column;">
        @foreach($leaderboards as $leaderboard)
            <hr size="1px" align="center" width="100%">
            <div style="display:flex;">
                <img style="width:30%;" src="{{ $leaderboard['photo'] }}">
                <div style="margin-left:10px;">
                    <p>暱稱:{{ $leaderboard['nickname'] }}</p>
                    <p>稱號:{{ $leaderboard['title'] }}</p>
                    <p>積分:{{ $leaderboard['point'] }}</p>
                    <p>排名:{{ $leaderboard['ranking'] }}</p>
                </div>            
            </div>
        @endforeach
    </div>
</div>

@endsection

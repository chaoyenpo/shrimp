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
@endphp

<h2>個人賽</h2>

<form method="get">
<input type="hidden" name="imei" value="{{$request->get('imei')}}">
<input type="hidden" name="api_token" value="{{$request->get('api_token')}}">
<div class="row">
    <div class="col-md-12 my-3 px-4">
        <select class="form-control" name="status" id="status">
            <option value="notend">進行中-未結束比賽</option>
            <option value="end">已結束-已結束比賽</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-12 my-3 px-4">
        <select class="form-control" name="location_catrgory" id="location_catrgory">
              <option value="">請選擇分區</option>
              <option>Ａ區（北海岸）</option>
              <option>Ｂ區（風城）</option>
              <option>Ｃ區（夜都）</option>
              <option>Ｄ區（南灣）</option>
              <option>Ｅ區（霹靂）</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-12 my-3 px-4">
        <select class="form-control" name="type" id="type">
              <option value="">請選擇分類</option>
              <option>蝦王爭霸積分賽</option>
              <option>社團比賽</option>
        </select>
    </div>
</div>

</form>

<script>
$(function(){
    @if (isset($_GET['status']))
        $("#status").val("{{ $_GET['status'] }}");
    @endif

    @if (isset($_GET['location_catrgory']))
        $("#location_catrgory").val("{{ $_GET['location_catrgory'] }}");
    @endif

    @if (isset($_GET['type']))
        $("#type").val("{{ $_GET['type'] }}");
    @endif

    $("#location_catrgory,#status,#type").change(function(){
        $('form').submit();
    });

});
</script>

<div class="row">
    <div class="col-md-12 my-3 px-4">
        <div class="list-group gamesList">
        @forelse($games as $game)
            <!-- <hr size="1px" align="center" width="100%"> -->
            @if ($user)
                @if (in_array($user['id'], $game['host_main_personnel_ids']) || in_array($user['id'], $game['game_member_ids']))
                <a class="list-group-item bg-warning list-group-item-action" href="{{ url('/api/game') }}/{{ $game['identifier'] }}?api_token={{ $api_token }}&imei={{ $imei }}">
                @else
                <a class="list-group-item list-group-item-action" href="{{ url('/api/game') }}/{{ $game['identifier'] }}?api_token={{ $api_token }}&imei={{ $imei }}">
                @endif
                <p>編號：{{ $game['identifier'] }}</p>
            @else
                <a class="list-group-item list-group-item-action" href="{{ url('/api/game') }}/{{ $game['identifier'] }}">
                <p>編號：{{ $game['identifier'] }}</p>
            @endif
                <p>日期：{{ explode(' ', $game['begin_at'])[0] }}</p>
                <p>名稱：{{ $game['name'] }}</p>
                <p>場地：{{ $game['shrimp_farm'] }}</p>
                <p>協辦：{{ $game['community'] }}</p>
                <p>報名人數：{{ $game['people_now'] }} / {{ $game['people_num'] }}</p>
                <p>贊助商：{{ $game['sponsor'] ?? '缺乾爹'}}</p>
                <p>分區：{{ $game['location_catrgory'] }}</p>
                <p>狀態：{{ $game['statusText'] }}</p>
            </a>
        @empty
            <p>目前無任何比賽</p>
        @endforelse
        </div>
    </div>
</div>

@endsection

@extends('activities.index')

@section('content')

<h2>比賽內容</h2>

<div class="row mb-3">
    <div class="col-md-3">比賽資訊</div>
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-6">比賽場地：{{ $game->shrimpFarm->name }}</div>
            <div class="col-md-6">比賽分區：{{ $game->location_catrgory }}</div>
        </div>
        <div class="row">
            <div class="col-md-6">比賽名稱：{{ $game->name }}</div>
            <div class="col-md-6">比賽編號：{{ $game->identifier }}</div>
        </div>
        <div class="row">
            <div class="col-md-6">協辦社團：{{ $game->community }}</div>
            <div class="col-md-6">贊助商：{{ $game->sponsor }}</div>
        </div>
        <div class="row">
            <div class="col-md-6">比賽人數：{{ $game->people_num }}（保留名額：{{ $game->host_quota }}）</div>
            <div class="col-md-6">報名人數：{{ $game->members(['ok','waiting','pending'])->count() }}</div>
        </div>
        <div class="row">
            <div class="col-md-6">比賽日期：{{ $game->begin_at }}</div>
            <div class="col-md-6">比賽狀態：{{ $game->statusText() }}</div>
        </div>
        <div class="row">
            <div class="col-md-12">備註：{{ $game->note }}</div>
        </div>
    </div>
</div>

@if ($game->members(['host_main_personnel','host_personnel'], $user->id)->first())

    <h2>報名比賽</h2>

    <div class="alert alert-warning" role="alert">工作人員不能報名比賽。</div>
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ url('api/game') }}/{{ $game->identifier }}?api_token={{ $user->api_token }}&imei={{ $user->imei }}" class="btn btn-primary" role="button" aria-pressed="true">返回</a>
        </div>
    </div>

@elseif ($game->members(['ok','waiting'], $user->id)->first() || (!empty($member) && $member->is_pay))

    <h2>取消報名比賽</h2>

    @if (!empty($result))
        <div class="alert alert-warning" role="alert">{{ $result }}</div>
    @endif
    <div class="row mb-3">
        <div class="col-md-12">確定要取消報名嗎？將扣除 100 點手續費，退還 {{ env('SIGNUP_PRICE')-100 }} 點報名費</div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12 text-center">
            <form method="POST">
                <input type="hidden" name="api_token" value="{{ $user->api_token }}">
                <input type="hidden" name="imei" value="{{ $user->imei }}">
                <input type="hidden" name="type" value="quit">
                <button type="submit" class="btn btn-primary">確定</button>
                <a href="{{ url('api/game') }}/{{ $game->identifier }}?api_token={{ $user->api_token }}&imei={{ $user->imei }}" class="btn btn-primary" role="button" aria-pressed="true">返回</a>
            </form>
        </div>
    </div>

@elseif ($game->members(['pending','host_quota'], $user->id)->first())

    <h2>比賽繳費</h2>

    @if (!empty($result))
        <div class="alert alert-warning" role="alert">{{ $result }}</div>
    @endif
    <div class="row mb-3">
        <div class="col-md-12">確定要繳費嗎？將扣除 {{ env('SIGNUP_PRICE') }} 點報名費</div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12 text-center">
            <form method="POST">
                <input type="hidden" name="api_token" value="{{ $user->api_token }}">
                <input type="hidden" name="imei" value="{{ $user->imei }}">
                <input type="hidden" name="type" value="pay">
                <button type="submit" class="btn btn-primary">確定</button>
                <a href="{{ url('api/game') }}/{{ $game->identifier }}?api_token={{ $user->api_token }}&imei={{ $user->imei }}" class="btn btn-primary" role="button" aria-pressed="true">返回</a>
            </form>
        </div>
    </div>

@else

    <h2>報名比賽</h2>

    @if (!empty($result))
        <div class="alert alert-warning" role="alert">{{ $result }}</div>
    @endif
    <div class="row mb-3">
        <div class="col-md-12">確定要報名嗎？</div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12 text-center">
            <form method="POST">
                <input type="hidden" name="api_token" value="{{ $user->api_token }}">
                <input type="hidden" name="imei" value="{{ $user->imei }}">
                <input type="hidden" name="type" value="join">
                <button type="submit" class="btn btn-primary">確定</button>
                <a href="{{ url('api/game') }}/{{ $game->identifier }}?api_token={{ $user->api_token }}&imei={{ $user->imei }}" class="btn btn-primary" role="button" aria-pressed="true">返回</a>
            </form>
        </div>
    </div>

@endif

@endsection

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

@if ($game->members(['host_main_personnel'], $user->id)->first())

    <h2>使用保留名額</h2>

    @if (!empty($result))
        <div class="alert alert-warning" role="alert">{{ $result }}</div>
    @endif
    <form method="POST">
        <div class="row mb-3">
            <div class="col-md-2">人員電話：</div>
            <div class="col-md-6">
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-2">新增或移除人員：</div>
            <div class="col-md-10">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="inlineRadio1" value="add" checked>
                    <label class="form-check-label mr-3" for="inlineRadio1">新增</label>
                    <input class="form-check-input" type="radio" name="type" id="inlineRadio2" value="remove">
                    <label class="form-check-label" for="inlineRadio2">移除</label>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <input type="hidden" name="api_token" value="{{ $user->api_token }}">
                <input type="hidden" name="imei" value="{{ $user->imei }}">
                <button type="submit" class="btn btn-primary">確定</button>
                <a href="{{ url('api/game') }}/{{ $game->identifier }}?api_token={{ $user->api_token }}&imei={{ $user->imei }}" class="btn btn-primary" role="button" aria-pressed="true">返回</a>
            </div>
        </div>
    </form>

@endif

@endsection

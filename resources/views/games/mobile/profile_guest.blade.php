@extends('activities.index')

@section('content')

<h2>
    {{ $profile->nickname }}
</h2>

<div class="row mb-3">
    <div class="col-6" id="photo_wrap">
        <img src="{{ $profile->photo }}">
    </div>
    <div class="col-6">
        <div class="row mb-3">
            <div class="col-md-3">有效積分：</div>
            <div class="col-md-9">{{ $profile->gameIntegral(52) }}</div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">累計積分：</div>
            <div class="col-md-9">{{ $profile->gameIntegral() }}</div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">斬蝦數：</div>
            <div class="col-md-9">{{ $profile->gamePoint() }}</div>
        </div>
    </div>
</div>

<h2>備註</h2>
<div class="row mb-3">
    <div class="col-md-12">{{ $profile->note }}</div>
</div>

<style>
#photo_wrap {
    max-height: 200px;
    max-width: 200px;
    padding-left: 30px;
}
#photo_wrap img {
    max-height: 100%;
    max-width: 100%;
}
@media (min-width: 768px) {
    #photo_wrap {
        max-height: 400px;
        max-width: 400px;
        padding-left: 30px;
    }
}
</style>

<h2>備戰賽事</h2>

<div class="row mb-3">
    <div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">賽事編號</th>
                    <th class="text-left">場地</th>
                    <th class="text-center">比賽日期</th>
                </tr>
            </thead>
            <tbody>
                @forelse($profile->gameMembers as $record)
                <tr>
                    <td class="text-center">
                        <a href="{{ url('api/game') }}/{{ $record->game->identifier }}?user_id={{ $record->user_id }}">{{ $record->game->identifier }}</a>
                    </td>
                    <td class="text-left">{{ $record->game->shrimpFarm->name }}</td>
                    <td class="text-center"><div>{{ explode(' ', $record->game->beginAtWithWeek())[0] }}</div></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">未參加任何賽事</td>
                </tr>
                @endforelse
            </tbody>
        </table>
</div>
    </div>
</div>

<h2>戰績表</h2>

<div class="row mb-3">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">賽事編號</th>
                    <th class="text-left">場地</th>
                    <th class="text-center">成績</th>
                    <th class="text-center">斬蝦</th>
                    <th class="text-center">積分</th>
                </tr>
            </thead>
            <tbody>
                @forelse($profile->gameResults as $record)
                <tr>
                    <td class="text-center">
                        <a href="{{ url('api/game') }}/{{ $record->game->identifier }}?user_id={{ $record->user_id }}">{{ $record->game->identifier }}</a>
                    </td>
                    <td class="text-left">{{ $record->game->shrimpFarm->name }}</td>
                    <td class="text-center">{{ $record->result ?? '未得名' }}</td>
                    <td class="text-center">{{ $record->member()->results()->sum('point') }}</td>
                    <td class="text-center">{{ $record->sumIntegral() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">未參加任何賽事</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

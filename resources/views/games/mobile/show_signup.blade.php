@php
    $request = \Illuminate\Support\Facades\Request::instance();
    $user_id = $request->get('user_id');
@endphp
<div class="table-responsive">
<table class="table table-bordered sigupListTable">
    <thead>
        <tr>
            <th width="5%" class="text-center">#</th>
            <th width="25%" class="text-center">報名時間</th>
            <th class="text-left">報名者</th>
            @if ($host_personnel || $host_main_personnel)
                <th width="10%" class="text-center">功能</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @php
            $status = ['ok','host_quota','pending'];
            if (in_array($game->status, ['sign_up', 'pay_up']))
                array_push($status, 'waiting');
        @endphp
        @forelse ($game->members($status)->get() as $key => $member)
            @php
                $image = '';
                $key = $rank_list[$member->user_id] ?? 99 + 1;
                $key += 1;
                $image = '';
                if ($key == 1) {
                    $image = '閃電黑';
                } else if ($key >= 2 && $key <= 13) {
                    $image = '閃電紅';
                } else if ($key >= 14 && $key <= 30) {
                    $image = '閃電紫';
                }
            @endphp
            <tr style="background-image: url('/{{$image}}.gif')" @if ($user && $member->user_id == $user->id) class="bg-self" @elseif ($member->user_id == $user_id) class="bg-target" @elseif (in_array($member->status, ['host_quota'])) class="bg-success" @elseif (in_array($member->status, ['waiting'])) style="background: #E0E0E0" @endif>
                <td class="text-center small">{{ $loop->iteration }}</td>
                <td class="text-center small">
                    <div>
                        {{ explode(' ', $member->register_at)[0] }}
                    </div>
                    <div>
                        {{ explode(' ', $member->register_at)[1] }}
                    </div>
                </td>
                <td class="text-left">
                    <div class="sigupList">
                        <div class="avatarContainer">
                           <img src="{{ $member->user->photo }}"  class="rounded-circle avatar"> 
                        </div>
                        <div class="userInfo" style="font-size: 15px">
                            <p>
                                @if ($user)
                                    <a href="{{ url('api/game') }}/profile/{{ $member->user_id }}?api_token={{ $user->api_token }}&imei={{ $user->imei }}">{{ ($host_personnel || $host_main_personnel) ? $member->user->nicknameWithFullPhone() : $member->user->nicknameWithPhone() }}</a><br>
                                @else
                                    <a href="{{ url('api/game') }}/profile/{{ $member->user_id }}">{{ $member->user->nickname }}</a><br>
                                @endif
                                @if ($member->status == 'ok')
                                    (繳費成功)
                                @elseif ($member->status == 'pending')
                                    (待繳費)
                                @elseif ($member->status == 'waiting')
                                    (候補)
                                @elseif ($member->status == 'host_quota')
                                    @if($member->is_pay)
                                    (保留繳費成功)
                                    @else
                                    (保留名額)
                                    @endif
                                @endif
                            </p>
                        
                            <p style="color: gray;">{{ $member->user->note }}</p>
                        </div>
                    </div>
                    
      
                </td>
                @if ($host_personnel || $host_main_personnel)
                    <td class="text-center">
                        @if (in_array($game->status, ['create','sign_up','prepare','pay_up']))
                            @if ($member->status == 'waiting')
                                <button class="btn btn-primary btn-sm hostquota" data-member_id="{{ $member->id }}" data-type="add">移入保留</button>
                                <!-- <button class="btn btn-primary btn-sm hostquota" data-member_id="{{ $member->id }}" data-type="remove">移出保留</button> -->
                            @else
                                @if ($member->is_lock == 1)
                                <button class="btn btn-primary btn-sm hostquota" data-member_id="{{ $member->id }}" data-type="unlock">解鎖退賽</button>
                                    @if ($member->is_pay != 1)
                                        <button class="btn btn-danger btn-sm hostquota" data-member_id="{{ $member->id }}" data-type="forceCancel">強迫退賽</button>
                                    @endif
                                @else
                                <button class="btn btn-primary btn-sm hostquota" data-member_id="{{ $member->id }}" data-type="lock">鎖定</button>
                                @endif
                            @endif
                        @elseif ($game->status == 'ing')
                            @php
                                $type = ($member->is_check_in) ? 0 : 1;
                                $text = ($member->is_check_in) ? '取消報到' : '報到';
                            @endphp
                            <button class="btn btn-primary btn-sm checkin" data-member_id="{{ $member->id }}" data-type="{{ $type }}">{{ $text }}</button>
                        @endif
                    </td>
                @endif
            </tr>
        @empty
            <tr>
                @if ($host_personnel || $host_main_personnel)
                    <td colspan="4" class="text-center">目前無人報名</td>
                @else
                    <td colspan="3" class="text-center">目前無人報名</td>
                @endif
            </tr>
        @endforelse
    </tbody>
</table>
</div>
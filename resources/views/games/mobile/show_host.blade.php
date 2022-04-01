@php
    $request = \Illuminate\Support\Facades\Request::instance();
    $user_id = $request->get('user_id');
@endphp
<div class="table-responsive">
<table class="table table-bordered sigupListTable">
    <thead>
        <tr>
            <th width="5%" class="text-center">#</th>
            <th width="25%" class="text-center">電話</th>
            <th class="text-left">協辦負責人</th>
            <th class="text-left">職位</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($game->members(['host_main_personnel'])->get() as $member)
            <tr @if ($user && $member->user_id == $user->id) class="bg-self" @elseif ($member->user_id == $user_id) class="bg-target" @endif>
                <td class="text-center small">{{ $loop->iteration }}</td>
                <td class="text-center small">
                    {{$member->user->phone}}
                </td>
                <td class="text-left">
                    <div class="sigupList">
                        <div class="avatarContainer">
                           <img src="{{ $member->user->photo }}"  class="rounded-circle avatar"> 
                        </div>
                        <div class="userInfo">
                            <p>
                                {{ $member->user->nicknameWithPhone() }}
                            </p>
                        </div>
                    </div>
                </td>
                <td>
                    主工作人員
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>
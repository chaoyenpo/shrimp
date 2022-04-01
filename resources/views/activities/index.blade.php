<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>蝦王爭霸</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="{{ mix('/css/app.css') }}"/>
        <link type="text/css" rel="stylesheet" href="/css/custom.css?20201"/>
        <link rel="shortcut icon" href="/logo2.png" >
        <script src="{{ mix('/js/app.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>
        <script>
            function change(path){
                window.location = window.location.origin + path;
            }
        </script>
    </head>
    <body>
        <div class="container-fluid">
        @php
            $request = \Illuminate\Support\Facades\Request::instance();
             $api_token = $request->get('api_token');
            $imei = $request->get('imei');
            $user = \App\Models\Profile\Entities\User
                    ::where('api_token', $api_token)
                    ->first();
         @endphp
            <div class="row sticky-top">
                <div class="col-md-12 text-center" style="background: rgb(23, 64, 115);">
                    <img src="{{ asset('logo.png') }}" class="logo">
                </div>
                <div class="col-md-12" style="background:rgb(15, 46, 77);">
                    <div class="dropdownLayout">
                        <div class="dropdown">
                            @if ($user)
                                <div id="home" onclick="change('/activities/game/single?api_token={{ $api_token }}&imei={{ $imei }}')">
                                    <span>賽程</span> 
                                </div>
                            @else
                                <div id="home" onclick="change('/activities/game/single')">
                                    <span>賽程</span> 
                                </div>
                            @endif
                        </div>
                        <div class="dropdown">
                            <span>規則</span>
                            <div class="dropdown-content">
                                @if ($user)
                                    <div onclick="window.changeRule('/activities/rule/depositrefund?api_token={{ $api_token }}&imei={{ $imei }}')">儲值＆退費</div>
                                    <div onclick="window.changeRule('/activities/rule/specifications?api_token={{ $api_token }}&imei={{ $imei }}')">比賽規格</div>
                                    <div onclick="window.changeRule('/activities/rule/registration?api_token={{ $api_token }}&imei={{ $imei }}')">報名</div>
                                    <div onclick="window.changeRule('/activities/rule/rule?api_token={{ $api_token }}&imei={{ $imei }}')">比賽規則</div>
                                    <div onclick="window.changeRule('/activities/rule/gamemode?api_token={{ $api_token }}&imei={{ $imei }}')">比賽模式</div>
                                    <div onclick="window.changeRule('/activities/rule/location?api_token={{ $api_token }}&imei={{ $imei }}')">賽事分區</div>
                                    <div onclick="window.changeRule('/activities/rule/yearend?api_token={{ $api_token }}&imei={{ $imei }}')">年終大獎賽</div>
                                    <div onclick="window.changeRule('/activities/rule/achievement?api_token={{ $api_token }}&imei={{ $imei }}')">成就獎勵</div>
                                    <div onclick="window.changeRule('/activities/rule/title?api_token={{ $api_token }}&imei={{ $imei }}')">稱號</div>
                                    <div onclick="window.changeRule('/activities/rule/result?api_token={{ $api_token }}&imei={{ $imei }}')">本季場次</div>
                                @else
                                    <div onclick="window.changeRule('/activities/rule/depositrefund')">儲值＆退費</div>
                                    <div onclick="window.changeRule('/activities/rule/specifications')">比賽規格</div>
                                    <div onclick="window.changeRule('/activities/rule/registration')">報名</div>
                                    <div onclick="window.changeRule('/activities/rule/rule')">比賽規則</div>
                                    <div onclick="window.changeRule('/activities/rule/gamemode')">比賽模式</div>
                                    <div onclick="window.changeRule('/activities/rule/location')">賽事分區</div>
                                    <div onclick="window.changeRule('/activities/rule/yearend')">年終大獎賽</div>
                                    <div onclick="window.changeRule('/activities/rule/achievement')">成就獎勵</div>
                                    <div onclick="window.changeRule('/activities/rule/title')">稱號</div>
                                    <div onclick="window.changeRule('/activities/rule/result')">本季場次</div>
                                @endif
                            </div>
                        </div>
                        <div class="dropdown">
                            <span>英雄榜</span>
                            <div class="dropdown-content">
                                @if ($user)
                                    <div onclick="change('/api/game/rank/integral?api_token={{ $api_token }}&imei={{ $imei }}')">積分排名</div>
                                    <div onclick="change('/api/game/rank/champion?api_token={{ $api_token }}&imei={{ $imei }}')">制霸成就</div>
                                    <div onclick="change('/api/game/rank/point?api_token={{ $api_token }}&imei={{ $imei }}')">斬蝦成就</div>
                                    <div onclick="change('/api/game/rank/pkking?api_token={{ $api_token }}&imei={{ $imei }}')">PK王成就</div>
                                    <div onclick="change('/api/game/rank/hotking?api_token={{ $api_token }}&imei={{ $imei }}')">熱血王成就</div>
                                    <div onclick="change('/api/game/rank/preChampion?api_token={{ $api_token }}&imei={{ $imei }}')">預冠王成就</div>

                                @else
                                    <div onclick="change('/api/game/rank/integral')">積分排名</div>
                                    <div onclick="change('/api/game/rank/champion')">制霸成就</div>
                                    <div onclick="change('/api/game/rank/point')">斬蝦成就</div>
                                    <div onclick="change('/api/game/rank/pkking')">PK王成就</div>
                                    <div onclick="change('/api/game/rank/hotking')">熱血王成就</div>
                                    <div onclick="change('/api/game/rank/preChampion')">預冠王成就</div>
                                @endif
                            </div>
                        </div>
                        <div class="dropdown">

                            @if ($user)
                                <div onclick="change('/api/game/profile/{{ $user->id }}?api_token={{ $api_token }}&imei={{ $imei }}')"><span>個人資訊</span></div> 
                            @else
                                <div onclick="change('/api/game/profile')"><span>個人資訊</span> </div>
                            @endif
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 p-2">
                @yield('content')
            </div>
        </div>
    </body>
</html>

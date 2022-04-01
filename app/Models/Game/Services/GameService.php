<?php

namespace App\Models\Game\Services;

use App\Models\Game\Entities\Game;
use App\Models\Game\Entities\GameMember;
use App\Models\Game\Entities\GameResult;
use App\Models\Profile\Entities\User;
use App\Models\System\Entities\PointRecord;
use Carbon\Carbon;
use App\Models\System\Services\FCMService;
use App\Models\Profile\Repositories\UserRepository;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;

class GameService
{
    static public function updateSignup($day)
    {
        $datetime = Carbon::now()->adddays(42)->format('Y-m-d') . " 21:00:00";

        return Game::where('status', 'create')
                   ->where('start_at', '<', Carbon::parse($datetime))
                   ->update(['status' => 'sign_up']);
    }

    static public function updatePrepare($day)
    {
        $realtimeDatabase = (new Factory)
            ->withServiceAccount(base_path().'/shrimp-king-firebase-adminsdk-wvabh-6b8f7bb223.json')
            ->withDatabaseUri('https://shrimp-king.firebaseio.com/')
            ->createDatabase();

        Game::where('status', 'sign_up')
            ->where('begin_at', '<', Carbon::now()->addHours(3))
            ->update(['status' => 'prepare']);

        $games = Game::where('status', 'prepare')
                     ->get();
        foreach ($games as $game) {
            $members = $game->members(['waiting'])->get();
            foreach ($members as $member) {
                $user = $member->user;
                PointRecord::create([
                    'category'     => 'Game',
                    'user_id'      => $member->user_id,
                    'point'        => (int) (env('SIGNUP_PRICE')),
                    'formData'     => ['game_id' => $game->id, 'identifier' => $game->identifier, 'type' => '候補失敗'],
                    'is_confirmed' => 1]);
                $user->point = $user->point + (env('SIGNUP_PRICE'));
                $user->save();

                // 同步會員點數至 Firebase
                $updates = [
                    "member/$user->firebase_uid/point" => $user->point,
                ];
                $realtimeDatabase->getReference()->update($updates);

                $member->delete();
            }

        }
    }

    static public function updatePending($service_fcm)
    {
        $games = Game::where('status', 'pay_up')
                     ->get();
        foreach ($games as $game) {
            self::pushPendingMemberByGame($service_fcm, $game->id);
        }
    }

    static public function pushPendingMemberByGame($service_fcm, $game_id)
    {
        $game = Game::find($game_id);
        $device_token = [];
        $members = $game->members(['pending', 'host_quota'])->where('is_pay', 0)->get();
        foreach ($members as $member) {
            $device_token[] = $member->user->device_token;

            // if (Carbon::now()->subDays(4)->gte(Carbon::parse($member->register_at))) {
            //     $device_token[] = $user->device_token;
            // }

            // if (Carbon::now()->subDays(7)->gte(Carbon::parse($member->register_at))) {
            //     $member->delete();
            // }
        }

        if (!empty($device_token)) {
            $response = $service_fcm->send2Devices($device_token, '攤位活動', "賽事 {$game->name} 已開放繳費，請儘速繳納，以保權益。", ['id' => $member->id, 'click_action' => 'FLUTTER_NOTIFICATION_CLICK', 'channel_id' => 'shrimpking']);
        }
        // \Log::info($response->getBody()->getContents());
    }


    static public function PushMessageForSignUp($service_fcm, $member)
    {
        $device_token = [];
        $host_members = $member->game->members(['host_main_personnel','host_personnel'])->get();

        foreach ($host_members as $host_member) {
            $device_token[] = $host_member->user->device_token;
        }

        if (!empty($device_token)) {
            $response = $service_fcm->send2Devices($device_token, '報名通知-'.$member->user->nicknameWithPhone(), $member->game->begin_at->format('Y-m-d') . '-' . $member->game->shrimpFarm->name, ['id' => $member->id, 'click_action' => 'FLUTTER_NOTIFICATION_CLICK', 'channel_id' => 'shrimpking']);
        }
    }


    static public function PushMessageForCancel($service_fcm, $member)
    {
        $device_token = [];
        $host_members = $member->game->members(['host_main_personnel','host_personnel'])->get();

        foreach ($host_members as $host_member) {
            $device_token[] = $host_member->user->device_token;
        }

        if (!empty($device_token)) {
            $response = $service_fcm->send2Devices($device_token, '退賽通知-'.$member->user->nicknameWithPhone(), $member->game->begin_at->format('Y-m-d') . '-' . $member->game->shrimpFarm->name, ['id' => $member->id, 'click_action' => 'FLUTTER_NOTIFICATION_CLICK', 'channel_id' => 'shrimpking']);
        }
    }

    static public function PushMessageForBegin($service_fcm)
    {
        $games = Game::where('status', 'sign_up')
                     ->where('begin_at', '>=', Carbon::today()->startOfDay())
                     ->where('begin_at', '<=', Carbon::today()->endOfDay())
                     ->get();
        foreach ($games as $game) {
            $device_token = [];
            $rate = $game->members(['ok', 'pending', 'waiting'])->count() / $game->people_num;
            if ($rate > 0.75) {
                $members = $game->members()->get();

                foreach ($members as $member) {
                    $user = User::where('phone', $member->phone)->first();
                    $device_token[] = $user->device_token;
                }

                $response = $service_fcm->send2Devices($device_token, '準備開板', '比賽準備開板囉！', ['id' => $member->id, 'click_action' => 'FLUTTER_NOTIFICATION_CLICK', 'channel_id' => 'shrimpking']);
                \Log::info($response->getBody()->getContents());
            }
        }
    }

    static public function updateIng()
    {
        return Game::whereIn('status', ['prepare', 'sign_up', 'pay_up'])
                   ->where('begin_at', '<', Carbon::now())
                   ->update(['status' => 'ing']);
    }

    static public function handlePrepare()
    {
        $realtimeDatabase = (new Factory)
            ->withServiceAccount(base_path().'/shrimp-king-firebase-adminsdk-wvabh-6b8f7bb223.json')
            ->withDatabaseUri('https://shrimp-king.firebaseio.com/')
            ->createDatabase();

        $games = Game::where('status', 'prepare')
                     ->get();
        foreach ($games as $game) {
            $nums = $game->members(['ok','waiting','host_quota'])->count();
            if ($nums < $game->people_num) {
                $game->update(['status' => 'cancel']);
                $members = $game->members(['ok','waiting','pending'])->get();
                foreach ($members as $member) {
                    $user = $member->user;
                    PointRecord::create([
                        'category'     => 'Game',
                        'user_id'      => $user->id,
                        'point'        => (int) env('SIGNUP_PRICE'),
                        'formData'     => ['game_id' => $game->id, 'identifier' => $game->identifier, 'type' => '比賽取消'],
                        'is_confirmed' => 1
                    ]);
                    $user->point = $user->point + env('SIGNUP_PRICE');
                    $user->save();

                    // 同步會員點數至 Firebase
                    $updates = [
                        "member/$user->firebase_uid/point" => $user->point,
                    ];
                    $realtimeDatabase->getReference()->update($updates);
                }
            }
        }
    }

    public function getRankList($type = 'integral')
    {
        $list = [];

        if ($type == 'champion') {
            $results = GameResult::with('user')
                                 ->where('level', 'final')
                                 ->where('result', 1)
                                 ->where(function ($query){
                                    $query->where('user_id', '>=', 7982)->orWhere('user_id', '<', 7782);
                                 })
                                 ->distinct()
                                 ->select('user_id')
                                 ->get();
            foreach ($results as $result) {
                $list[$result->user_id] = (int) $result->user->gameChampionCount();
            }
        } else if ($type == 'pkking') {
            $results = GameResult::with('user')
                                 ->where('result', 'like', '%PK%')
                                 ->where(function ($query){
                                    $query->where('user_id', '>=', 7982)->orWhere('user_id', '<', 7782);
                                 })
                                 ->distinct()
                                 ->select('user_id')
                                 ->get();
            foreach ($results as $result) {
                $list[$result->user_id] = (int) $result->user->gamePKCount();
            }
        } else if ($type == 'hotking') {
            $results = GameResult::with('user')
                                 ->where('level', 'round1')
                                 ->where(function ($query){
                                    $query->where('user_id', '>=', 7982)->orWhere('user_id', '<', 7782);
                                 })
                                 ->distinct()
                                 ->select('user_id')
                                 ->get();
            foreach ($results as $result) {
                $list[$result->user_id] = (int) $result->user->gameJoinCount();
            }
        } else if ($type == 'preChampion') {
            $results = GameResult::with('user')
                                 ->where('level', 'round1')
                                 ->where(function ($query){
                                    $query->where('user_id', '>=', 7982)->orWhere('user_id', '<', 7782);
                                 })
                                 ->distinct()
                                 ->select('user_id')
                                 ->get();
            foreach ($results as $result) {
                $list[$result->user_id] = (int) $result->user->gamePreChampionCount();
            }
        } else {
            $results = GameResult::with('user')
                                 ->where('point', '>', 0)
                                 ->where('integral', '>', 0)
                                 ->where(function ($query){
                                    $query->where('user_id', '>=', 7982)->orWhere('user_id', '<', 7782);
                                 })
                                 ->distinct()
                                 ->select(['user_id', 'created_at'])
                                 ->orderBy('created_at', 'asc')
                                 ->get();

            if ($type == 'integral') {
                foreach ($results as $result) {
                    $list[$result->user_id] = (int) $result->user->gameIntegral();
                }
            } elseif ($type == 'integral52') {
                foreach ($results as $result) {
                    $list[$result->user_id] = [
                        'point' => (int) $result->user->gameIntegral(52),
                        'created_at' => $result->created_at
                    ];
                }
            } elseif ($type == 'point') {
                foreach ($results as $result) {
                    $list[$result->user_id] = (int) $result->user->gamePoint();
                }
            }
        }

        if ($type == 'integral52') {
            $list = collect($list)->sortByDesc(function ($item, $key) {
                return $item['point'] * 100000000 - $item['created_at']->timestamp;
            });
        } else {
            arsort($list);
        }

        return $list;
    }
}

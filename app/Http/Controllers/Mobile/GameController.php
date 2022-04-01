<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Game\Repositories\GameRepository;
use App\Models\Game\Repositories\GameMemberRepository;
use App\Models\Game\Repositories\GameResultRepository;
use App\Models\Game\Entities\GameMember;
use App\Models\Game\Entities\GameResult;
use App\Models\Game\Services\GameService;
use App\Models\System\Repositories\PointRecordRepository;
use App\Models\Profile\Repositories\UserRepository;
use Carbon\Carbon;
use App\Models\Profile\Entities\User;
use sngrl\PhpFirebaseCloudMessaging\Client;
use App\Models\System\Services\FCMService;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;

class GameController extends Controller
{
    private $repository;
    private $repository_member;
    private $repository_result;
    private $repository_point;
    private $repository_user;

    public function __construct(
        GameRepository $repository,
        GameMemberRepository $repository_member,
        GameResultRepository $repository_result,
        PointRecordRepository $repository_point,
        UserRepository $repository_user
    ) {
        $this->repository = $repository;
        $this->repository_member = $repository_member;
        $this->repository_result = $repository_result;
        $this->repository_point = $repository_point;
        $this->repository_user = $repository_user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = $this->repository->listForBackend();

        return view('games.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Game\Forms\GameFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GameFormRequest $request)
    {
        $user = $request->user();
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $user = $this->repository_user->where('api_token', '=', $request->api_token)
                                      ->where('imei', '=', $request->imei)
                                      ->first();

// $members = [
// '0923303876',
// '0913060911',
// '0976895500',
// '0910202824',
// '0938665878',
// '0939891360',
// '0935130126',
// '0978637909',
// '0973833422',
// '0966123680',
// '0983496897',
// '0925921219',
// '0963833087',
// '0929730923',
// '0925337128',
// '0923737987',
// '0921146349',
// '0900779268',
// '0938560675',
// '0980671888',
// '0980499596',
// '0935155966',
// '0932976846',
// '0937139321'
// ];

// $point = [
// '5',
// '7',
// '7',
// '10',
// '13',
// '7',
// '8',
// '5',
// '7',
// '1',
// '10',
// '9',
// '9',
// '4',
// '9',
// '5',
// '12',
// '12',
// '10',
// '6',
// '7',
// '10',
// '6',
// '8',
// ];

// foreach ($members as $key => $member) {
//     $user = $this->repository_user->where('phone', '=', $member)
//                                   ->first();

//     GameResult::create([
//         'user_id' => $user->id,
//         'game_id' => $game->id,
//         'level' => 'final',
//         'number' => $key + 1,
//         'point' => $point[$key]
//     ]);
// }
// die;
        $rate = $game->members(['ok', 'pending', 'waiting'])->count() / $game->people_num;

        $result = $request->result;
        if ($user)
            $can_sugnup = $this->repository_member->canNotSignup($user->id, $game->id) ? false : true;
        else
            $can_sugnup = false;

        // $champion_ids = [];
        // foreach ([[1,24], [25,48]] as $number) {
        //     $pre_champion = $this->game->results('round1', $number)
        //     ->where(function ($query)
        //      {
        //           $query->where('is_pk_win', 1)
        //                 ->where('result', '冠軍PK')
        //                 ->orWhere('result', 1);
        //      })
        //     ->first();
        //     $champion_ids[] = $pre_champion['user_id'];
        // }

        // $bet_result = $this->game->results()->select('user_id', \DB::raw('SUM(point) as sum_point'))
        // ->orderBy('sum_point', 'DESC')
        // ->groupBy('user_id')
        // ->first();
        // $champion_ids[] = $bet_result['user_id'];
        // $this->game->results('final')->orderBy('result', 'asc');

        $game_results = GameResult::selectRaw('sum(integral) as integral, user_id')
                        ->groupBy('user_id')
                        ->where('game_id', '=', $game->id)
                        ->orderBy('integral', 'desc')
                        ->pluck('user_id')
                        ->toArray();

        $game_results = array_flip($game_results);

        $service = new GameService();
        $rank_list = array_flip(array_keys($service->getRankList('integral52')->toArray()));

        return view('games.mobile.show', compact('rank_list', 'game_results', 'game','user','result', 'can_sugnup', 'rate'));
    }

    /**
     * Show the form for signup.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function showSignupForm(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $user = $request->user();
        $result = null;

        if ($game->status == 'create') {
            $result = '該比賽目前尚未開放報名。';
        } elseif (!in_array($game->status, ['sign_up', 'pay_up'])) {
            $result = '已過報名時間。';
        } elseif ($this->repository_member->canNotSignup($user->id, $game->id)) {
            $result = '一天只能參加一場賽事。';
        }
        if ($result)
            return redirect('api/game/'.$game->identifier.'?api_token='.$user->api_token.'&imei='.$user->imei.'&result='.$result);

        $member = $this->repository_member->where('user_id', '=', $user->id)
                                ->where('game_id', '=', $game->id)
                                ->first();

        return view('games.mobile.signup', compact('game','user','member'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function handleSignup(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $user = $request->user();
        $result = null;

        $member = $this->repository_member->where('user_id', '=', $user->id)
                                ->where('game_id', '=', $game->id)
                                ->first();

        // if (!empty($member->status) && $member->status == 'pending' && $request->type == 'quit') {
        //     $this->repository_member->where('user_id', '=', $user->id)
        //                             ->where('game_id', '=', $game->id)
        //                             ->delete();
        //     $nums     = $game->people_num - $game->host_quota;
        //     $nums_now = $game->members(['ok', 'pending'])->count();

        //     if ($member->status != 'pending') {
        //         $this->repository_point->save([
        //             'category'     => 'Game',
        //             'user_id'      => $user->id,
        //             'point'        => (int) (env('SIGNUP_PRICE') - 100),
        //             'formData'     => ['game_id' => $game->id, 'identifier' => $game->identifier, 'type' => '退賽'],
        //             'is_confirmed' => 1]);
        //         $user->point = $user->point + (env('SIGNUP_PRICE') - 100);
        //         $user->save();
        //     }

        //     $result = '取消報名成功。';

        //     if ($nums_now < $nums) {
        //         $member = $game->members('waiting')->first();
        //         if ($member) {
        //             $member->update(['status' => 'pending']);
        //         }
        //     }
        //     return redirect('api/game/'.$game->identifier.'?api_token='.$user->api_token.'&imei='.$user->imei.'&result='.$result);
        // }

        if ($game->status == 'create') {
            $result = '該比賽目前尚未開放報名。';
        } elseif ($game->status == 'ing' || $game->status == 'end') {
            $result = '已過報名時間。';
        } elseif (!in_array($game->status, ['sign_up', 'pay_up'])) {
            $result = '已過報名時間。';
        } elseif ($game->members(['host_main_personnel','host_personnel'], $user->id)->first()) {
            $result = '工作人員不能報名比賽。';
        } elseif ($request->type == 'quit' && ($game->status == 'pay_up' && ($member->is_lock == 1 && $member->status != 'waiting'))) {
            $result = '目前階段退賽要由主辦方解鎖才可退賽。';
            return redirect('api/game/'.$game->identifier.'?api_token='.$user->api_token.'&imei='.$user->imei.'&result='.$result);
        } elseif ($request->type == 'join' && $this->repository_member->canNotSignup($user->id, $game->id)) {
            $result = '一天只能參加一場賽事。';
        } elseif ($request->type == 'pay') {
            if ($user->point >= env('SIGNUP_PRICE')) {
                $nums     = $game->people_num - $game->host_quota;
                $nums_now = $game->members(['ok', 'pending'])->count();
                $member->is_pay = 1;

                if ($member->status == 'pending') {
                    $member->status = 'ok';
                }
                $member->save();

                $this->repository_point->save([
                    'category'     => 'Game',
                    'user_id'      => $user->id,
                    'point'        => (int) ('-'.env('SIGNUP_PRICE')),
                    'formData'     => ['game_id' => $game->id, 'identifier' => $game->identifier, 'type' => '報名'],
                    'is_confirmed' => 1]);
                $user->point = $user->point - env('SIGNUP_PRICE');
                $user->save();

                // 同步會員點數至 Firebase
                $realtimeDatabase = (new Factory)
                    ->withServiceAccount(base_path().'/shrimp-king-firebase-adminsdk-wvabh-6b8f7bb223.json')
                    ->withDatabaseUri('https://shrimp-king.firebaseio.com/')
                    ->createDatabase();

                $updates = [
                    "member/$user->firebase_uid/point" => $user->point,
                ];
                $realtimeDatabase->getReference()->update($updates);

                $result = '繳費成功。';
            } else {
                $result = '報名費為 '.env('SIGNUP_PRICE').' 點，請儲值。';
            }
            return redirect('api/game/'.$game->identifier.'?api_token='.$user->api_token.'&imei='.$user->imei.'&result='.$result);
        } elseif (in_array($request->type, ['join', 'quit'])) {
            $this->repository_member->where('user_id', '=', $user->id)
                                    ->where('game_id', '=', $game->id)
                                    ->delete();
            $nums     = $game->people_num - $game->host_quota;
            $nums_now = $game->members(['ok', 'pending'])->count();

            if ($request->type == 'join') {
                $member = $this->repository_member->save([
                    'user_id'     => $user->id,
                    'game_id'     => $game->id,
                    'status'      => ($nums_now < $nums) ? 'pending' : 'waiting',
                    'register_at' => date('Y-m-d H:i:s')
                ]);
                $result = '報名成功。';

                $client = new Client;
                $service_fcm = new FCMService($client);
                GameService::PushMessageForSignUp($service_fcm, $member);

                return redirect('api/game/'.$game->identifier.'?api_token='.$user->api_token.'&imei='.$user->imei.'&result='.$result);
            } else {
                if ($member->status != 'pending') {
                    $this->repository_point->save([
                        'category'     => 'Game',
                        'user_id'      => $user->id,
                        'point'        => (int) (env('SIGNUP_PRICE') - 100),
                        'formData'     => ['game_id' => $game->id, 'identifier' => $game->identifier, 'type' => '退賽'],
                        'is_confirmed' => 1]);
                    $user->point = $user->point + (env('SIGNUP_PRICE') - 100);
                    $user->save();

                    // 同步會員點數至 Firebase
                    $realtimeDatabase = (new Factory)
                        ->withServiceAccount(base_path().'/shrimp-king-firebase-adminsdk-wvabh-6b8f7bb223.json')
                        ->withDatabaseUri('https://shrimp-king.firebaseio.com/')
                        ->createDatabase();

                    $updates = [
                        "member/$user->firebase_uid/point" => $user->point,
                    ];
                    $realtimeDatabase->getReference()->update($updates);
                }
                $result = '取消報名成功。';
                $client = new Client;
                $service_fcm = new FCMService($client);
                GameService::PushMessageForCancel($service_fcm, $member);
                $this->repository_member->where('user_id', '=', $user->id)
                                        ->where('game_id', '=', $game->id)
                                        ->delete();

                if ($nums_now < $nums) {
                    $member = $game->members('waiting')->first();
                    if ($member) {
                        $member->update(['status' => 'pending']);
                    }
                }
            }
            return redirect('api/game/'.$game->identifier.'?api_token='.$user->api_token.'&imei='.$user->imei.'&result='.$result);
        }
        return view('games.mobile.signup', compact('game','user','result','member'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function handleHostquota(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $host = $request->user();
        $result = null;
        $host_personnel      = $game->members(['host_personnel'], $host->id)->first() ? true : false;
        $host_main_personnel = $game->members(['host_main_personnel'], $host->id)->first() ? true : false;

        if (!in_array($game->status, ['create','sign_up','prepare','pay_up'])) {
            $result = '只可在比賽進行前使用保留名額。';
        } elseif ($host_personnel || $host_main_personnel) {
            $member = $this->repository_member->getById($request->member_id); RTIfEntityNotFound($member);
            if ($this->repository_member->checkHost($member->user_id, $game->id)) {
                $result = '該人員為工作人員。';
            } elseif ($request->type == 'unlock') {
                // if (Carbon::now()->lt($game->begin_at->subdays(3))) {
                //     $member->is_lock = 0;
                //     $member->save();
                //     $result = '已解鎖。';
                // } else {
                //     $result = '賽前 3 天不可解鎖。';
                // }
                $member->is_lock = 0;
                $member->save();
                $result = '已解鎖。';
            } elseif ($request->type == 'lock') {
                $member->is_lock = 1;
                $member->save();
                $result = '已鎖定。';
            // } elseif ($request->type == 'remove' && Carbon::now()->gt($game->begin_at->subdays(3))) {
            } elseif ($request->type == 'remove') {
                $result = '賽前 3 天不可退賽。';
            } else {
                $nums      = $game->people_num - $game->host_quota;
                $nums_host = $game->members('host_quota')->count();
                $nums_now  = $game->members('ok')->count();

                if ($request->type == 'add') {
                    if ($game->host_quota > $nums_host) {
                        $this->repository_member->where('user_id', '=', $member->user_id)
                                                ->where('game_id', '=', $game->id)
                                                ->where('status', '=', 'waiting')
                                                ->update(['status' => 'host_quota']);

                        $result = '已加入保留名額。';
                    } else {
                        $result = '保留名額已用罄。';
                    }

                } elseif ($request->type == 'remove') {
                    $status = $member->is_pay ? 'ok' : 'pending';
                    $status = ($nums_now < $nums) ? $status : 'waiting';

                    $this->repository_member->where('user_id', '=', $member->user_id)
                                            ->where('game_id', '=', $game->id)
                                            ->where('status', '=', 'host_quota')
                                            ->update(['status' => $status]);

                    $result = '已移出保留名額。';
                }
            }

            $user = $host;
            // return view('games.mobile.show_signup', compact('result', 'game','user','host_personnel','host_main_personnel'));
            // 

            return [
                'view' => view('games.mobile.show_signup', compact('result', 'game','user','host_personnel','host_main_personnel'))->render(),
                'result' => $result
            ];
        } else {
            RTErrorString('權限不足。只有工作人員方可使用保留名額。');
        }
    }

    /**
     * Checkin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function checkin(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $user = $request->user();

        if ($game->status != 'ing') {
            RTErrorString('比賽尚未開始。');
        } elseif ($game->members(['host_main_personnel'], $user->id)->first()) {
            $is_check_in = ($request->type) ? 1 : 0;

            $this->repository_member->where('game_id', '=', $game->id)
                                    ->where('id', '=', $request->member_id)
                                    ->whereNotIn('status', ['host_main_personnel', 'host_personnel'])
                                    ->update(['is_check_in' => $is_check_in]);
            return $is_check_in;
        } else {
            RTErrorString('權限不足。主工作人員方可使用報到和取消報到功能。');
        }
    }

    /**
     * Random.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function random(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $user = $request->user();
        $host_personnel      = $game->members(['host_personnel'], $user->id)->first() ? true : false;
        $host_main_personnel = $game->members(['host_main_personnel'], $user->id)->first() ? true : false;

        if ($host_main_personnel) {
            $state = $request->level .'-lock-number';
            if (is_array($game->progress) && in_array($state, $game->progress)) {
                RTErrorString('號次已鎖定，無法抽籤。');
            } else {
                $state = $request->level .'-random';
                $game->progress = [$state];
                $game->save();
            }

            $this->repository_result->where('game_id', '=', $game->id)
                                    ->where('level', '=', $request->level)
                                    ->where('number', '>=', 1)
                                    ->where('number', '<=', $game->people_num)
                                    ->delete();

            if ($request->prev == '') {
                $numbers = range(1, $game->people_num);
                shuffle($numbers);

                $members = $this->repository_member->where('game_id', '=', $game->id)
                                                   ->whereIn('status', ['ok', 'host_quota'])
                                                   ->take($game->people_num)
                                                   ->get();
                if ($members->count() == $game->people_num) {
                    foreach ($members as $member) {
                        $this->repository_result->save([
                            'user_id' => $member->user_id,
                            'game_id' => $member->game_id,
                            'level'   => $request->level,
                            'number'  => array_pop($numbers)
                        ]);
                    }
                } else {
                    RTErrorString('參賽人數不足。');
                }
            } else {
                $numbers = range(1, $game->people_num/$game->mode);
                shuffle($numbers);

                $this->repository_result->where('game_id', '=', $game->id)
                                        ->where('level', '=', $request->prev)
                                        ->update(['can_edit' => 0]);
                $progress = $game->progress;
                array_push($progress, 'final-random');
                $game->progress = $progress;
                $game->save();

                $results = $this->repository_result->where('game_id', '=', $game->id)
                                ->where('level', '=', $request->prev)
                                ->where(function($query) {
                                    return $query->where('is_pk_win', '=', 1)
                                                 ->orWhere(function($query) {
                                                        return $query->where('result', '<>', '晉級PK')
                                                                     ->where('result', '<>', NULL);
                                                     });
                                })
                                ->pluck('user_id')
                                ->toArray();
                if (count($results) == (($request->max-$request->min)+1)) {
                    foreach ($results as $result) {
                        $this->repository_result->save([
                            'user_id' => $result,
                            'game_id' => $game->id,
                            'level'   => $request->level,
                            'number'  => array_pop($numbers)
                        ]);
                    }
                } else {
                    RTErrorString('晉級人數不足，請重新確認');
                }
            }
            return view('games.mobile.show_round', compact('game','user','host_personnel','host_main_personnel'));
        } else {
            RTErrorString('權限不足。主工作人員方可使用此功能。');
        }
    }

    /**
     * Lock Number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function lockNumber(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $user = $request->user();
        $host_personnel      = $game->members(['host_personnel'], $user->id)->first() ? true : false;
        $host_main_personnel = $game->members(['host_main_personnel'], $user->id)->first() ? true : false;

        if ($host_main_personnel) {
            $state = $request->level .'-lock-number';
            if (empty($game->progress)) {
                $game->progress = [$state];
                $game->save();
            } elseif (is_array($game->progress) && !in_array($state, $game->progress)) {
                $progress = $game->progress;
                array_push($progress, $state);
                $game->progress = $progress;
                $game->save();
            }
            return view('games.mobile.show_round', compact('game','user','host_personnel','host_main_personnel'));
        } else {
            RTErrorString('權限不足。主工作人員方可使用此功能。');
        }
    }

    /**
     * Auto Point.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function autopoint(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $user = $request->user();
        $host_personnel      = $game->members(['host_personnel'], $user->id)->first() ? true : false;
        $host_main_personnel = $game->members(['host_main_personnel'], $user->id)->first() ? true : false;

        if ($host_main_personnel) {
            $results = $this->repository_result->where('game_id', '=', $game->id)
                                               ->where('level', '=', $request->level)
                                               ->where('number', '>=', $request->min)
                                               ->where('number', '<=', $request->max)
                                               ->where('can_edit', '=', 1)
                                               ->get();
            foreach ($results as $result) {
                $result->update(['point' => rand(1, 15), 'result' => NULL, 'is_pk_win' => 0]);
            }

            return view('games.mobile.show_round', compact('game','user','host_personnel','host_main_personnel'));
        } else {
            RTErrorString('權限不足。主工作人員方可使用此功能。');
        }
    }

    /**
     * Update Point.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function updatePoint(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $result = $this->repository_result->getById($request->result_id); RTIfEntityNotFound($result);
        $user = $request->user();

        if ($game->members(['host_main_personnel'], $user->id)->first()) {
            $host_main_personnel = true;
            if ($result->can_edit)
                $result->update(['point' => $request->point]);

            return $result->point;
        } else {
            RTErrorString('權限不足。主工作人員方可使用此功能。');
        }
    }

    /**
     * Update Rank.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function updateRank(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $user = $request->user();
        $host_personnel      = $game->members(['host_personnel'], $user->id)->first() ? true : false;
        $host_main_personnel = $game->members(['host_main_personnel'], $user->id)->first() ? true : false;

        if ($host_main_personnel) {
            if ($request->level == 'final') {
                $results = $this->repository_result->where('game_id', '=', $game->id)
                                                   ->where('level', '=', $request->level)
                                                   ->where('number', '>=', $request->min)
                                                   ->where('number', '<=', $request->max)
                                                   ->where('point', '<>', NULL)
                                                   ->orderBy('point', 'DESC')
                                                   ->get();
                $rank = 1;
                $count = 0;
                foreach ($results as $key=>$result) {
                    if (isset($results[$key+1])) {
                        if ($result->point == $results[$key+1]->point) {
                            if ($key > 0 && $result->point != $results[$key-1]->point) {
                                $rank++;
                            }
                            if ($rank <= 5) {
                                $result->update(['result' => $rank.'-PK']);
                                $results[$key+1]->update(['result' => $rank.'-PK']);
                            } else {
                                $result->update(['result' => $rank]);
                                $results[$key+1]->update(['result' => $rank]);
                            }
                            $count += 1;
                        } elseif ($key == 0) {
                            $result->update(['result' => 1]);
                        } else {
                            if ($count > 0) {
                                $rank += $count;
                                $count = 0;
                            }
                            if ($result->point != $results[$key-1]->point) {
                                $rank++;
                                $result->update(['result' => $rank]);
                            }
                        }
                    } else {
                        if ($result->point != $results[$key-1]->point) {
                            $rank++;
                            $result->update(['result' => $rank]);
                        }
                    }
                }
            } else {
                $this->repository_result->where('game_id', '=', $game->id)
                                        ->where('level', '=', $request->level)
                                        ->where('number', '>=', $request->min)
                                        ->where('number', '<=', $request->max)
                                        ->update([
                                            'result' => NULL,
                                            'is_pk_win' => 0
                                        ]);
                $results = $this->repository_result->where('game_id', '=', $game->id)
                                                   ->where('level', '=', $request->level)
                                                   ->where('number', '>=', $request->min)
                                                   ->where('number', '<=', $request->max)
                                                   ->where('point', '<>', NULL)
                                                   ->orderBy('point', 'DESC')
                                                   ->get();

                $flag_pk = $game->people_num/($game->mode*$game->mode)-1;

                $flag_pk_before = true;
                $flag_pk_after = true;
                $point_pk = $results[$flag_pk]->point;
                do {
                    $flag_pk = $flag_pk - 1;
                    if (!isset($results[$flag_pk])) {
                        $flag_pk_before = false;
                    } elseif ($point_pk == $results[$flag_pk]->point) {
                        $results[$flag_pk]->update(['result' => '晉級PK']);
                        $results[$flag_pk+1]->update(['result' => '晉級PK']);
                    } else {
                        $flag_pk_before = false;
                    }
                } while ($flag_pk_before);
                $flag_pk = $game->people_num/($game->mode*$game->mode)-1;
                do {
                    $flag_pk = $flag_pk + 1;
                    if (!isset($results[$flag_pk])) {
                        $flag_pk_after = false;
                    } elseif ($point_pk == $results[$flag_pk]->point) {
                        $results[$flag_pk]->update(['result' => '晉級PK']);
                        $results[$flag_pk-1]->update(['result' => '晉級PK']);
                    } else {
                        $flag_pk_after = false;
                    }
                } while ($flag_pk_after);

                $rank = 1;
                $count = 0;
                $nums = 0;
                $point_1 = $results[0]->point;
                foreach ($results as $key=>$result) {
                    if ($result->result == '晉級PK' || $key == $game->people_num/$game->mode/$game->mode ) break;

                    $nums++;
                    if ($result->point == $results[$key+1]->point) {
                        if ($key > 0 && $result->point != $results[$key-1]->point) {
                            $rank++;
                        }
                        if ($result->point == $point_1) {
                            $result->update(['result' => '冠軍PK']);
                            $results[$key+1]->update(['result' => '冠軍PK']);
                        } else {
                            $result->update(['result' => $rank]);
                            $results[$key+1]->update(['result' => $rank]);
                        }
                        $count += 1;
                    } elseif ($key == 0) {
                        $result->update(['result' => 1]);
                    } else {
                        if ($count > 0) {
                            $rank += $count;
                            $count = 0;
                        }
                        if ($result->point != $results[$key-1]->point) {
                            $rank++;
                            $result->update(['result' => $rank]);
                        }
                    }
                }
                $nums_pk = 0;
                foreach ($results as $result) {
                    if ($result->result == '晉級PK')
                        $nums_pk++;
                }
                if ($nums+$nums_pk == $game->people_num/($game->mode*$game->mode)) {
                    foreach ($results as $result) {
                        if ($result->result == '晉級PK')
                            $result->update(['result' => $rank]);
                    }
                }
            }

            return view('games.mobile.show_round', compact('game','user','host_personnel','host_main_personnel'));
        } else {
            RTErrorString('權限不足。主工作人員方可使用此功能。');
        }
    }

    /**
     * Update PK.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function updatePK(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $result = $this->repository_result->getById($request->result_id); RTIfEntityNotFound($result);
        $user = $request->user();
        $host_personnel      = $game->members(['host_personnel'], $user->id)->first() ? true : false;
        $host_main_personnel = $game->members(['host_main_personnel'], $user->id)->first() ? true : false;

        if ($host_main_personnel) {
            if ($request->level == 'final' && !is_integer($request->result)) {
                $records = $this->repository_result->where('game_id', '=', $game->id)
                                                   ->where('level', '=', $request->level)
                                                   ->pluck('result')
                                                   ->toArray();
                $numbers = [];
                foreach ($records as $record) {
                    if (is_numeric($record))
                        array_push($numbers, $record);
                }
                for ($i=1; $i<=$game->people_num/$game->mode; $i++) {
                    if (in_array($i, $numbers)) continue;
                    if ($i == 5) {
                        $this->repository_result->where('game_id', '=', $game->id)
                                                ->where('level', '=', $request->level)
                                                ->where('result', '5-PK')
                                                ->update(['result' => 6]);
                    }
                    $result->update(['result' => $i]);
                    break;
                }
            } else {
                if ($result->result == '冠軍PK') {
                    $this->repository_result->where('game_id', '=', $game->id)
                                            ->where('level', '=', $request->level)
                                            ->where('number', '>=', $request->min)
                                            ->where('number', '<=', $request->max)
                                            ->where('result', $result->result)
                                            ->update(['is_pk_win' => 0, 'integral' => NULL]);
                    $result->update(['is_pk_win' => 1, 'integral' => ceil($game->people_num / 8)]);
                }
                if ($result->result == '晉級PK') {
                    if ($result->is_pk_win == 1) {
                        $result->update(['is_pk_win' => 0]);
                    } else {
                        $result->update(['is_pk_win' => 1]);
                        $now = $this->repository_result->where('game_id', '=', $game->id)
                                                        ->where('level', '=', $request->level)
                                                        ->where('number', '>=', $request->min)
                                                        ->where('number', '<=', $request->max)
                                                        ->where('result', '<>', NULL)
                                                        ->where('result', '<>', $result->result)
                                                        ->count();
                        $pk_win = $this->repository_result->where('game_id', '=', $game->id)
                                                        ->where('level', '=', $request->level)
                                                        ->where('number', '>=', $request->min)
                                                        ->where('number', '<=', $request->max)
                                                        ->where('result', '=', $result->result)
                                                        ->where('is_pk_win', '=', 1)
                                                        ->count();
                        $diff = $game->people_num/4 - $now;
                        if ($pk_win > $diff) {
                            $count = $pk_win-$diff;
                            $this->repository_result->where('game_id', '=', $game->id)
                                                    ->where('level', '=', $request->level)
                                                    ->where('number', '>=', $request->min)
                                                    ->where('number', '<=', $request->max)
                                                    ->where('result', $result->result)
                                                    ->where('is_pk_win', '=', 1)
                                                    ->orderBy('updated_at', 'ASC')
                                                    ->orderBy('id', 'ASC')
                                                    ->take($count)
                                                    ->update(['is_pk_win' => 0]);
                        }
                    }
                }
            }

            return view('games.mobile.show_round', compact('game','user','host_personnel','host_main_personnel'));
        } else {
            RTErrorString('權限不足。主工作人員方可使用此功能。');
        }
    }

    /**
     * Update Integral.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function updateIntegral(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $user = $request->user();
        $host_personnel      = $game->members(['host_personnel'], $user->id)->first() ? true : false;
        $host_main_personnel = $game->members(['host_main_personnel'], $user->id)->first() ? true : false;

        $pre_champion_ids = [];
        foreach ([[1,$game->people_num/$game->mode], [$game->people_num/$game->mode+1,$game->people_num]] as $number) {
            $pre_champion = $game->results('round1', $number)
            ->where(function ($query)
             {
                  $query->where('is_pk_win', 1)
                        ->where('result', '冠軍PK')
                        ->orWhere('result', 1);
             })
            ->first();
            $pre_champion_ids[] = $pre_champion['user_id'];
        }

        //預賽冠軍積分
        $this->repository_result->where('game_id', '=', $game->id)
        ->where('level', '=', 'round1')
        ->whereIn('user_id', $pre_champion_ids)
        ->update(['integral' => ceil($game->people_num/8)]);

        $records = $this->repository_result->where('game_id', '=', $game->id)
                                           ->where('level', '=', $request->level)
                                           ->orderBy('result', 'ASC')
                                           ->get();

        if ($host_main_personnel) {
            if ($request->level == 'final') {
                $records = $this->repository_result->where('game_id', '=', $game->id)
                                                   ->where('level', '=', $request->level)
                                                   ->orderBy('result', 'ASC')
                                                   ->get();
                $flag = true;
                foreach ($records as $record) {
                    if (!is_numeric($record->result)) {
                        $flag = false;
                    }
                }
                if ($flag) {
                    foreach ($records as $record) {
                        switch ($record->result) {
                            case 1:
                                $integral = $game->people_num < 48 ? ceil($game->people_num/2) : floor($game->people_num/2);
                                $record->update(['integral' => $integral]);
                                break;
                            case 2:
                                $integral = $game->people_num < 48 ? ceil($game->people_num/3) : floor($game->people_num/3);
                                $record->update(['integral' => $integral]);
                                break;
                            case 3:
                                $integral = $game->people_num < 48 ? ceil($game->people_num/4) : floor($game->people_num/4);
                                $record->update(['integral' => $integral]);
                                break;
                            case 4:
                                $integral = $game->people_num < 48 ? ceil($game->people_num/6) : floor($game->people_num/6);
                                $record->update(['integral' => $integral]);
                                break;
                            case 5:
                                $integral = $game->people_num < 48 ? ceil($game->people_num/8) : ceil($game->people_num/8);
                                $record->update(['integral' => $integral]);
                                break;
                            default:
                                $integral = $game->people_num < 48 ? ceil($game->people_num/24) : floor($game->people_num/24);
                                $record->update(['integral' => $integral]);
                        }
                    }
                    $progress = $game->progress;
                    array_push($progress, 'final-integral');
                    $game->progress = $progress;
                    $game->save();
                } else {
                    RTErrorString('請先完成排名作業。');
                }
            } else {
                $flag = true;
                foreach ($records as $record) {
                    if (!is_numeric($record->result)) {
                        $flag = false;
                    }
                }
                if ($flag) {
                    // if (in_array($record->user_id, $pre_champion_ids)) {
                    //     $record->increment('integral', ceil($game->people_num/8));
                    // }
                }
            }

            return view('games.mobile.show_round', compact('game','user','host_personnel','host_main_personnel'));
        } else {
            RTErrorString('權限不足。主工作人員方可使用此功能。');
        }
    }

    /**
     * End Game.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function end(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $user = $request->user();
        $host_personnel      = $game->members(['host_personnel'], $user->id)->first() ? true : false;
        $host_main_personnel = $game->members(['host_main_personnel'], $user->id)->first() ? true : false;

        if ($host_main_personnel) {
            $game->update(['status' => 'end']);

            return view('games.mobile.show_round', compact('game','user','host_personnel','host_main_personnel'));
        } else {
            RTErrorString('權限不足。主工作人員方可使用此功能。');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $type
     * @return \Illuminate\Http\Response
     */
    public function showRank(Request $request, $type)
    {
        $service = new GameService();
        if ($type == 'integral') {
            $list = $service->getRankList('integral52');
            $title = '積分排名';
        } elseif ($type == 'champion') {
            $list = $service->getRankList('champion');
            $title = '制霸成就';
        } elseif ($type == 'point') {
            $list = $service->getRankList('point');
            $title = '斬蝦成就';
        } elseif ($type == 'pkking') {
            $list = $service->getRankList('pkking');
            $title = 'PK王成就';
        } elseif ($type == 'hotking') {
            $list = $service->getRankList('hotking');
            $title = '熱血王成就';
        } elseif ($type == 'preChampion') {
            $list = $service->getRankList('preChampion');
            $title = '預冠王成就';
        } else {
            RTErrorString('參數錯誤。');
        }

        $records = [];
        $count = 0;
        $login_user = $this->repository_user->where('api_token', '=', $request->api_token)
                                      ->where('imei', '=', $request->imei)
                                      ->first(); 
        $pass = false;
        foreach ($list as $key=>$item) {
            if ($count++ == 100 && (!$login_user || $pass)) break;

            $user = $this->repository_user->getById($key);
            if ($login_user && $user->id == $login_user->id) {
                $pass = true;
            }

            $records = array_merge($records, [$key => [
                'id'            => $user->id,
                'rank'          => $count,
                'photo'         => $user->photo,
                'nickname'      => $user->nickname,
                'title'         => $user->title,
                'note'          => $user->note,
                'integral'      => $user->gameIntegral(52),
                'point'         => $user->gamePoint(),
                'created_at'         => $item['created_at'],
                'championCount' => $user->gameChampionCount(),
                'pkCount' => $user->gamePKCount(),
                'preChampionCount' => $user->gamePreChampionCount(),
                'joinCount' => $user->gameJoinCount(),
            ]]);
        }

        if ($login_user) {
            if (!$pass) {
                $records = array_merge($records, [$login_user->id => [
                    'id'            => $login_user->id,
                    'rank'          => $count + 1,
                    'photo'         => $login_user->photo,
                    'nickname'      => $login_user->nickname,
                    'title'         => $login_user->title,
                    'note'          => $login_user->note,
                    'integral'      => $login_user->gameIntegral(52),
                    'point'         => $login_user->gamePoint(),
                    'championCount' => $login_user->gameChampionCount(),
                    'pkCount' => $user->gamePKCount(),
                    'preChampionCount' => $user->gamePreChampionCount(),
                    'joinCount' => $user->gameJoinCount(),
                ]]);
            }
        }

        $rank_list = array_flip(array_keys($service->getRankList('integral52')->toArray()));

        return view('activities.game.rank', compact('rank_list', 'records', 'title', 'type'));
    }

    public function forceCancel(Request $request, $identifier)
    {
        $user = $this->repository_member->getById($request->member_id); RTIfEntityNotFound($user);
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $nums      = $game->people_num - $game->host_quota;
        $nums_host = $game->members('host_quota')->count();
        $nums_now  = $game->members('ok')->count();
        $host = $request->user();
        $host_personnel      = $game->members(['host_personnel'], $host->id)->first() ? true : false;
        $host_main_personnel = $game->members(['host_main_personnel'], $host->id)->first() ? true : false;

        if ($host_personnel || $host_main_personnel) {
            if ($user->is_pay == 1) {
                $this->repository_point->save([
                    'category'     => 'Game',
                    'user_id'      => $user->id,
                    'point'        => (int) (env('SIGNUP_PRICE') - 100),
                    'formData'     => ['game_id' => $game->id, 'identifier' => $game->identifier, 'type' => '退賽'],
                    'is_confirmed' => 1]);
                $user->point = $user->point + (env('SIGNUP_PRICE') - 100);
                $user->save();

                // 同步會員點數至 Firebase
                $realtimeDatabase = (new Factory)
                    ->withServiceAccount(base_path().'/shrimp-king-firebase-adminsdk-wvabh-6b8f7bb223.json')
                    ->withDatabaseUri('https://shrimp-king.firebaseio.com/')
                    ->createDatabase();

                $updates = [
                    "member/$user->firebase_uid/point" => $user->point,
                ];
                $realtimeDatabase->getReference()->update($updates);
            }
            $result = '強迫退賽成功。';
            $client = new Client;
            $service_fcm = new FCMService($client);
            GameService::PushMessageForCancel($service_fcm, $user);
            $user->delete();

            if ($nums_now < $nums) {
                $user = $game->members('waiting')->first();
                if ($user) {
                    $user->update(['status' => 'pending']);
                }
            }

            return [
                'view' => view('games.mobile.show_signup', compact('result', 'game','user','host_personnel','host_main_personnel'))->render(),
                'result' => $result
            ];

        } else {
            RTErrorString('權限不足。只有工作人員方可使用保留名額。');
        }
    }

    /**
     * Redirect to activities.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        return redirect('activities');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Int  $id
     * @return \Illuminate\Http\Response
     */
    public function showProfile(Request $request, $id)
    {
        $profile = $this->repository_user->getById($id); RTIfEntityNotFound($profile);
        $user = $this->repository_user->where('api_token', '=', $request->api_token)
                                      ->where('imei', '=', $request->imei)
                                      ->first();

        if ($user && $user->id == $id)
            return view('games.mobile.profile', compact('profile','user'));
        else
            return view('games.mobile.profile_guest', compact('profile'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
    }

    /**
     * Auto Sign up.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function autosignup(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $user = $request->user();
        $host_personnel      = $game->members(['host_personnel'], $user->id)->first() ? true : false;
        $host_main_personnel = $game->members(['host_main_personnel'], $user->id)->first() ? true : false;

        $last_number = 7782 + $game->people_num - 1;
        if ($host_main_personnel) {
            $this->repository_member->where('game_id', '=', $game->id)
                                    ->where('user_id', '>=', 7782)
                                    ->where('user_id', '<=', $last_number)
                                    ->delete();
            $nums     = $game->people_num - $game->host_quota;

            for ($i = 7782; $i<= $last_number; $i++) {
                $nums_now = $game->members('ok')->count();
                $status = ($nums_now < $nums) ? 'ok' : 'waiting';
                $this->repository_member->save([
                    'user_id'     => $i,
                    'game_id'     => $game->id,
                    'status'      => $status,
                    'register_at' => date('Y-m-d H:i:s')
                ]);
            }

            return view('games.mobile.show_signup', compact('game','user','host_personnel','host_main_personnel'));
        } else {
            RTErrorString('權限不足。主工作人員方可使用此功能。');
        }
    }

    /**
     * Reset Sign up.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $identifier
     * @return \Illuminate\Http\Response
     */
    public function resetSignup(Request $request, $identifier)
    {
        $game = $this->repository->findByIdentifier($identifier); RTIfEntityNotFound($game);
        $user = $request->user();
        $host_personnel      = $game->members(['host_personnel'], $user->id)->first() ? true : false;
        $host_main_personnel = $game->members(['host_main_personnel'], $user->id)->first() ? true : false;

        if ($host_main_personnel) {
            $this->repository_member->where('game_id', '=', $game->id)
                                    ->whereNotIn('status', ['host_main_personnel', 'host_personnel'])
                                    ->delete();
            $this->repository_result->where('game_id', '=', $game->id)
                                    ->delete();
            $game->update(['progress' => null]);

            return view('games.mobile.show_signup', compact('game','user','host_personnel','host_main_personnel'));
        } else {
            RTErrorString('權限不足。主工作人員方可使用此功能。');
        }
    }
}

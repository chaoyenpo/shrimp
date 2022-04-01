<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Profile\Forms\UserForm;
use App\Models\Profile\Forms\UserFormRequest;
use App\Models\Profile\Forms\UserPushSwitcherFormRequest;
use App\Models\Profile\Repositories\UserRepository;
use App\Models\System\Repositories\PointRecordRepository;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;

class MemberController extends Controller
{
    private $repository;
    private $repository_point;
    private $form;

    public function __construct(
        UserRepository $repository,
        UserForm $form,
        PointRecordRepository $repository_point
    ) {
        $this->repository = $repository;
        $this->repository_point = $repository_point;
        $this->form = $form;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = $this->repository->list();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    public function check(Request $request)
    {
        $data = ['imei'  => $request->imei,
                 'email' => $request->email,
                 'phone' => $request->phone];

        if (!empty($data['email']) && $this->form->checkExistEmail($data['imei'], $data['email']) > 0)
            RTErrorString('email 不可重複');
        elseif (!empty($data['phone']) && $this->form->checkExistPhone($data['imei'], $data['phone']) > 0)
            RTErrorString('手機號碼已被註冊');
        else
            return response()->json(['success' => true]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Models\Profile\Forms\UserFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserFormRequest $request)
    {
        $data = ['api_token'    => Str::random(60),
                 'device_token' => $request->device_token,
                 'firebase_uid' => $request->firebase_uid,
                 'email'        => $request->email,
                 'nickname'     => $request->nickname,
                 'photo'        => 'https://firebasestorage.googleapis.com/v0/b/shrimp-king.appspot.com/o/user%2Fdefault.png?alt=media&token=2774cc0a-9d40-4401-90a4-4a4318434330'];
        $this->repository->save($data);

        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $record = $request->user();

        return response()->json(['nickname'              => $record->nickname,
                                 'email'                 => $record->email,
                                 'phone'                 => $record->phone,
                                 'photo'                 => $record->photo,
                                 'note'                  => $record->note,
                                 'point'                 => $record->point,
                                 'can_push_booth_1'      => $record->can_push_booth_1,
                                 'can_push_booth_2'      => $record->can_push_booth_2,
                                 'can_push_shrimp_event' => $record->can_push_shrimp_event,
                                 'is_vendor'             => $record->is_vendor,
                                 'is_shrimper'           => $record->is_shrimper,
                                 'is_recycler'           => $record->is_recycler]);
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
     * @param  App\Models\Profile\Forms\UserFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserFormRequest $request, $id)
    {
        $user = $request->user();
        $nickname_old = $user->nickname;

        $data = ['id'           => $user->id,
                 'device_token' => $request->device_token,
                 'imei'         => $request->imei,
                 'phone'        => $request->phone ?? $user->phone,
                 'nickname'     => $request->nickname,
                 'photo'        => $request->photo,
                 'note'         => $request->note,
                 'location_lat' => $request->location_lat,
                 'location_lng' => $request->location_lng];
        $user->update($data);

        if ($user->nickname != $nickname_old) {
            if ($user->point - 100 < 0) {
                RTErrorString('點數不足');
            } elseif ($user->nickname_count > 0) {
                $this->repository_point->save([
                    'category'     => 'Profile',
                    'user_id'      => $user->id,
                    'point'        => (int) -100,
                    'formData'     => ['nickname_old' => $nickname_old, 'nickname_new' => $user->nickname, 'nickname_count_old' => $user->nickname_count],
                    'is_confirmed' => 1]);
                $user->point = $user->point - 100;
                $user->nickname_count = $user->nickname_count+1;
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
            } else {
                $user->update([
                    'nickname_count' => $user->nickname_count+1
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Models\Profile\Forms\UserPushSwitcherFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePushSwitcher(UserPushSwitcherFormRequest $request)
    {
        $record = $request->user();

        $data = ['can_push_booth_1'      => $request->can_push_booth_1,
                 'can_push_booth_2'      => $request->can_push_booth_2,
                 'can_push_shrimp_event' => $request->can_push_shrimp_event];
        $record->update($data);

        return response()->json(['success' => true]);
    }

    public function listPoint(Request $request)
    {
        $user = $request->user();

        $data = [];
        foreach ($user->pointRecords(1)->get() as $record) {
            array_push($data, [
                'user_point' => $user->point,
                'id'         => $record->id,
                'category'   => $record->category,
                'text'   => $record->text,
                'point'      => $record->point,
                'formData'   => $record->formData,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
        }

        return response()->json($data);
    }

    public function listLikeFarms(Request $request)
    {
        $record = $request->user();

        $data = [];
        foreach ($record->likeFarms as $farm) {
            array_push($data, [
                'id'          => $farm->id,
                'name'        => $farm->name,
                'address'     => $farm->address,
                'score'       => $farm->evaluations->count() == 0 ? null : $farm->evaluations->sum('score') / $farm->evaluations->count()
            ]);
        }

        return response()->json($data);
    }

    public function login(Request $request)
    {
        $record = $this->repository->loginUsingFirebaseUid($request->firebase_uid);
        if ($record){
        	if (!empty($request->imei)) {
                $record2 = $this->repository->getByIMEI($request->imei);
                if ($record2 && $record2->id != $record->id)
                    $record2->update(['imei' => '']);
        	}

            $data = ['imei'     => $request->imei,
                     'is_login' => 1,
                     'login_at' => date("Y-m-d H:i:s")];
            $record->update($data);

            return response()->json(['success'   => true,
                                     'user_id'   => $record->id,
                                     'api_token' => $record->api_token]);
        }else{
            return response()->json(['success' => false]);
        }
    }

    public function logout(Request $request)
    {
        $data = ['is_login' => 0];

        $record = $request->user();
        $record->update($data);

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
    }
}
<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Recycler\Forms\RecyclerFormRequest;
use App\Models\Recycler\Repositories\RecyclerRepository;
use App\Models\Profile\Repositories\UserRepository;
use App\Models\System\Repositories\PointRecordRepository;
use Carbon\Carbon;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;

class RecyclerController extends Controller
{
    public function __construct(RecyclerRepository $repository, UserRepository $repository_user, PointRecordRepository $repository_point) {
        $this->repository = $repository;
        $this->repository_user  = $repository_user;
        $this->repository_point  = $repository_point;
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function recycleShrimp(RecyclerFormRequest $request)
    {
        $recycler = $request->user();
        $member_phone = $request->phone;

        if (!$recycler->is_recycler) {
            return response()->json([
                'success' => false,
                'error'=> '非 Recycler',
            ]);
        }

        // 售蝦人
        $member = $this->repository_user->getByPhone($member_phone);
        if (!$member) {
            return response()->json([
                'success' => false,
                'error'=> '手機號碼有誤',
            ]);
        }

        $original_weight = floatval($request->weight);
        $weight = floor($original_weight * 4) / 4; // 以 0.25 為一單位

        if ($weight <= 0) {
            return response()->json([
                'success' => false,
                'error'=> '斤數錯誤',
            ]);
        }
        $point = $weight * 300;
        $fee = 0; // 保留之後計算手續費

        $recycle_time = Carbon::now()->format('Y-m-d H:i:s');

        $record = [
            'recycler_name' => $recycler->nickname,
            'recycler_id' => $recycler->id,
            'member_name' => $member->nickname,
            'member_id' => $member->id,
            'member_phone' => $member->phone,
            'recycle_time' => $recycle_time,
            'weight' => $weight,
            'point' => $point,
            'fee' => $fee,
            'note' => NULL
        ];

        \DB::beginTransaction();
            try {
                // 寫入回蝦記錄表
                $this->repository->save($record);

                // 售蝦仁增加點數
                $member->point += $point;
                $member->save();

                // 寫入點數記錄表 - 售蝦人
                $this->repository_point->save([
                    'category' => 'RecycleShrimp',
                    'user_id' => $member->id,
                    'point' => $point,
                    'returnData' => [
                        'text' => "回蝦 $weight 斤"
                    ],
                    'is_confirmed' => 1,
                    'created_at' => $recycle_time,
                    'updated_at' => $recycle_time
                ]);

                \DB::commit();
            } catch (\Exception $e){
                \DB::rollback();

                return response()->json([
                    'success' => false,
                    'error'=> '發生非預期錯誤',
                ]);
            }
        
        // 同步會員點數至 Firebase
        $realtimeDatabase = (new Factory)
            ->withServiceAccount(base_path().'/shrimp-king-firebase-adminsdk-wvabh-6b8f7bb223.json')
            ->withDatabaseUri('https://shrimp-king.firebaseio.com/')
            ->createDatabase();
        
        // $firebase_user_point = $realtimeDatabase->getReference('member/UaNMIkn6lFfZ0hT3NRNtMmrpkF02/point')->getValue();

        $updates = [
            "member/$member->firebase_uid/point" => $member->point,
        ];
        $realtimeDatabase->getReference()->update($updates);
        
        // 取得本週回蝦總斤數
        $monday = Carbon::now();
        $monday->setTime(0, 0, 0);
        if ($monday->dayOfWeek == 0) {
            $monday->subDay(6);
        } else {
            $monday->subDay($monday->dayOfWeek - 1);
        }

        $week_weight = $this->repository->where('created_at', '>=', $monday->toDateTimeString())
                                        ->where('created_at', '<', $monday->addDay(7)->toDateTimeString())
                                        ->sum('weight');

        // 發送通知
        $url = "https://notify-api.line.me/api/notify";    
        $content = [
            'message' => "\n回蝦人: $recycler->nickname\n售蝦人: $member->nickname\n斤數: $weight ($original_weight)\n點數: $point\n本周累計: $week_weight 斤\n\n時間: $recycle_time",
        ];

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-type: application/x-www-form-urlencoded",
            "Authorization: Bearer MATwwtVWSFstiZ9DrvaosfjK0LN5qJCoZk0oL28noBz"
        ));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($content));
        $result = curl_exec($curl);

        return response()->json([
            'success' => true,
            'data' => [
                'recycler_name' => $recycler->nickname,
                'member_name' => $member->nickname,
                'original_weight' => $original_weight,
                'weight' => $weight,
                'point' => $point,
                'recycle_time' => $recycle_time,
            ],
            'msg'=> '回蝦成功',
        ]);
    }
}
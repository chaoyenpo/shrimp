<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Transfer\Repositories\TransferRepository;
use App\Models\Transfer\Forms\TransferFormRequest;
use App\Models\Profile\Repositories\UserRepository;
use App\Models\System\Repositories\PointRecordRepository;
use Carbon\Carbon;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;

class TransferPointController extends Controller
{
    public function __construct(TransferRepository $repository, UserRepository $repository_user, PointRecordRepository $repository_point) {
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
    public function transferPoint(TransferFormRequest $request)
    {
        $giver = $request->user();
        $taker_phone = $request->phone;
        $note = $request->note;

        $taker = $this->repository_user->getByPhone($taker_phone);
        if (!$taker) {
            return response()->json([
                'success' => false,
                'error'=> '手機號碼有誤',
            ]);
        }

        $point = intval($request->point);

        if ($giver->point < $point) {
            return response()->json([
                'success' => false,
                'error'=> '點數不足',
            ]);
        }

        $fee = $point * 0.05;
        $fee = min(intval($fee), 100); // 手續費無條件捨且最高 100 元
        $take_oint = $point - $fee; // 最終移轉點數扣除手續費

        if ($take_oint <= 0) {
            return response()->json([
                'success' => false,
                'error'=> '移轉點數小於 0',
            ]);
        }

        $created_at = Carbon::now()->format('Y-m-d H:i:s');

        $record = [
            'giver_id' => $giver->id,
            'taker_id' => $taker->id,
            'point' => $take_oint,
            'fee' => $fee,
            'note' => $note,
            'is_confirmed' => 1,
            'created_at' => $created_at,
            'updated_at' => $created_at
        ];

        \DB::beginTransaction();
        try {
            $giver->point -= $point;
            $giver->save();

            if ($giver->point < 0) {
                throw new Exception("點數不足");
            }

            $taker->point += $take_oint;
            $taker->save();

            // 寫入移轉記錄表
            $this->repository->save($record);


            // 寫入點數記錄表 - 支付者
            $this->repository_point->save([
                'category' => 'TransferPoint',
                'user_id' => $giver->id,
                'point' => -$point,
                'returnData' => [
                    'text' => "轉出至 $taker->nickname"
                ],
                'is_confirmed' => 1,
                'created_at' => $created_at,
                'updated_at' => $created_at
            ]);

            // 寫入點數記錄表 - 拿取者
            $this->repository_point->save([
                'category' => 'TransferPoint',
                'user_id' => $taker->id,
                'point' => $take_oint,
                'returnData' => [
                    'text' => "接收自 $giver->nickname"
                ],
                'is_confirmed' => 1,
                'created_at' => $created_at,
                'updated_at' => $created_at
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

        $updates = [
            "member/$giver->firebase_uid/point" => $giver->point,
            "member/$taker->firebase_uid/point" => $taker->point,
        ];
        $realtimeDatabase->getReference()->update($updates);


        return response()->json([
            'success' => true,
            'data' => [
                'giver_name' => $giver->nickname,
                'taker_name' => $taker->nickname,
                'point' => $take_oint,
                'fee' => $fee,
                'created_at' => $created_at
            ],
            'msg'=> '轉點成功',
        ]);
    }
}
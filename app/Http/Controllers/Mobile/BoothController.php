<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Profile\Entities\User;
use App\Models\Booth\Forms\BoothFormRequest;
use App\Models\Booth\Forms\BoothFormSearchRequest;
use App\Models\Booth\Forms\BoothOrderForm;
use App\Models\System\Forms\EvaluationFormRequest;
use App\Models\Booth\Repositories\BoothRepository;
use App\Models\Booth\Repositories\BoothOrderRepository;
use App\Models\System\Repositories\EvaluationRepository;
use App\Models\System\Services\FCMService;
use App\Models\Profile\Repositories\UserRepository;
use Carbon\Carbon;

class BoothController extends Controller
{
    private $repository;
    private $repository_order;
    private $repository_evaluation;
    private $repository_user;
    private $form_order;
    private $service_fcm;

    public function __construct(BoothRepository $repository,
                                BoothOrderRepository $repository_order,
                                EvaluationRepository $repository_evaluation,
                                UserRepository $repository_user,
                                BoothOrderForm $form_order,
                                FCMService $service_fcm)
    {
        $this->repository       = $repository;
        $this->repository_order = $repository_order;
        $this->repository_evaluation = $repository_evaluation;
        $this->repository_user  = $repository_user;
        $this->form_order = $form_order;
        $this->service_fcm = $service_fcm;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  App\Models\Booth\Forms\BoothFormSearchRequest
     * @return \Illuminate\Http\Response
     */
    public function index(BoothFormSearchRequest $request)
    {
        if (empty($request->api_token) || empty($request->imei)) {
            $user_id = null;
        } else {
            $user = User::where('api_token', $request->api_token)
                        ->where('imei', $request->imei)
                        ->first();

            $user_id = ($user && $request->type == 'self') ? $user->id : null;
        }

        return $this->repository->list($user_id, $request->location_lat, $request->location_lng);
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
     * @param  App\Models\Booth\Forms\BoothFormRequest
     * @return \Illuminate\Http\Response
     */
    public function store(BoothFormRequest $request)
    {
        $user = $request->user();

        $count = $this->repository->countSelfBooth($user->id);

        if (!$user->is_shrimper && $count >= 1)
            RTErrorString('同時僅能開設 1 攤');
        elseif ($count >= 5)
            RTErrorString('同時僅能開設 5 攤');

        $booth = $this->repository->save(['user_id'      => $user->id,
                                          'category'     => $request->category,
                                          'commodity'    => $request->commodity,
                                          'weight'       => $request->weight,
                                          'price'        => $request->price,
                                          'status'       => $request->status,
                                          'note'         => $request->note,
                                          'address'      => $request->address,
                                          'location_lat' => $request->location_lat,
                                          'location_lng' => $request->location_lng, 
                                          'begin_at'     => $request->begin_at,
                                          'end_at'       => $request->end_at,
                                          'is_enabled'   => 1]);

        $type = '';
        if ($request->category == 1) {
            $type = '賣蝦';
            $data = ['can_push_booth_1' => 1];
            $msg = '有人賣蝦摟，快進來看看';
        }
        elseif ($request->category == 2) {
            $type = '買蝦';
            $data = ['can_push_booth_2' => 1];
            $msg = '附近有人要買蝦，快進來看看';
        }

        if (in_array($request->category, [1, 2])) {
            $input_array = $this->repository_user->listWithinMaxDistance($request->location_lat, $request->location_lng, env('PUSH_DISTANCE'), $data);
            $records = array_chunk($input_array, 500);
            foreach ($records as $tokens) {
                $response = $this->service_fcm->send2Devices($tokens, '攤位活動', $msg, ['id' => $booth->id, 'click_action' => 'FLUTTER_NOTIFICATION_CLICK', 'channel_id' => 'shrimpking']);
                \Log::info($response->getBody()->getContents());
            }
        }

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
        $record = $this->repository->getById($id); RTIfEntityNotFound($record);
        $user = $request->user();

        if ($user->id != $record->user_id)
            RTErrorString('權限不足');

        $see_me_list = [];
        foreach ($record->orders as $order) {
        	if ($order->is_close) continue;
            array_push($see_me_list, ['id'           => $order->id,
                                      'can_evaluate' => $order->can_evaluate,
                                      'log'          => $order->customer_log]);
        }

        return response()->json(['category'     => $record->category,
                                 'commodity'    => $record->commodity,
                                 'weight'       => $record->weight,
                                 'price'        => $record->price,
                                 'status'       => $record->status,
                                 'note'         => $record->note,
                                 'address'      => $record->address,
                                 'location_lat' => $record->location_lat,
                                 'location_lng' => $record->location_lng,
                                 'begin_at'     => $record->begin_at->format('Y-m-d H:i:s'),
                                 'end_at'       => $record->end_at ? $record->end_at->format('Y-m-d H:i:s') : NULL,
                                 'is_enabled'   => $record->is_enabled,
                                 'see_me_list'  => $see_me_list]);
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
     * @param  App\Models\Booth\Forms\BoothFormRequest
     * @return \Illuminate\Http\Response
     */
    public function update(BoothFormRequest $request, $id)
    {
        $record = $this->repository->getById($id); RTIfEntityNotFound($record);
        $user = $request->user();

        if ($user->id != $record->user_id)
            RTErrorString('祇可以更新自己的攤位');

        $count = $this->repository->countSelfBooth($user->id, $record->id);
        if (empty($user->is_shrimper) && $count >= 1)
            RTErrorString('同時僅能開設 1 攤');
        elseif ($count >= 5)
            RTErrorString('同時僅能開設 5 攤');

        $record->update(['category'     => $request->category,
                         'commodity'    => $request->commodity,
                         'weight'       => $request->weight,
                         'price'        => $request->price,
                         'status'       => $request->status,
                         'note'         => $request->note,
                         'address'      => $request->address,
                         'location_lat' => $request->location_lat,
                         'location_lng' => $request->location_lng, 
                         'begin_at'     => $request->begin_at,
                         'end_at'       => $request->end_at,
                         'is_enabled'   => $request->is_enabled]);

        $type = '';
        if ($request->category == 1) {
            $type = '賣蝦';
            $data = ['can_push_booth_1' => 1];
            $msg = '有人賣蝦摟，快進來看看';
        }
        elseif ($request->category == 2) {
            $type = '買蝦';
            $data = ['can_push_booth_2' => 1];
            $msg = '附近有人要買蝦，快進來看看';
        }

        if ($request->is_enabled && in_array($request->category, [1, 2])) {
            $input_array = $this->repository_user->listWithinMaxDistance($request->location_lat, $request->location_lng, env('PUSH_DISTANCE'), $data);
            $records = array_chunk($input_array, 500);
            foreach ($records as $tokens) {
                $response = $this->service_fcm->send2Devices($tokens, '攤位活動', $msg, ['id' => $record->id, 'click_action' => 'FLUTTER_NOTIFICATION_CLICK', 'channel_id' => 'shrimpking']);
                \Log::info($response->getBody()->getContents());
            }
        }

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


    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $string
     * @return Boolean
     */
    public function isJSON($string){
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    /**
     * See
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function see(Request $request, $id)
    {
        $booth = $this->repository->getById($id); RTIfEntityNotFound($booth);
        $user = $request->user();

        if (!$booth->is_enabled) {
            RTErrorString('該攤位已關閉');
        } elseif ($booth->user_id == $user->id) {
            return response()->json(['success' => true]);
        } elseif ($this->form_order->checkExistUnFinishedOrder($id, $user->id)) {
            return response()->json(['success' => true]);
        } elseif (empty($request->customer) && !$this->isJSON($request->customer)) {
            RTErrorString('顧客資料為必填，且為 JSON 字串');
        } elseif (empty($request->booth) && !$this->isJSON($request->booth)) {
            RTErrorString('攤位資料為必填，且為 JSON 字串');
        }
        \Log::info($request->customer);
        \Log::info($request->booth);

        $this->repository_order->firstOrCreate(['booth_id'     => $booth->id,
                                                'customer_id'  => $user->id,
                                                'can_evaluate' => 0,
                                                'customer_log' => $request->customer,
                                                'booth_log'    => $request->booth,
                                                'is_close'     => 0]);

        $tokens = [$booth->user->device_token];
        $response = $this->service_fcm->send2Devices($tokens, '攤位通知', '有會員查看了您的攤位', ['id' => $booth->id, 'type' => 2, 'click_action' => 'FLUTTER_NOTIFICATION_CLICK', 'channel_id' => 'shrimpking']);
        \Log::info($response->getBody()->getContents());
        //var_dump($response->getBody()->getContents());

        return response()->json(['success' => true]);
    }

    /**
     * Confirm the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  int  $order_id
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request, $id, $order_id)
    {
        $booth = $this->repository->getById($id); RTIfEntityNotFound($booth);
        $user = $request->user();

        if ($booth->user_id == $user->id) {
            $order = $this->repository_order->getById($order_id); RTIfEntityNotFound($order);
            if ($order->is_close)
                RTErrorString('該交易已結束');

            $order->update(['can_evaluate' => 1]);
            $user->update(['sale_count' => $user->sale_count+1]);
            $order->user->update(['buy_count' => $order->user->buy_count+1]);

            return response()->json(['success' => true]);
        } else {
            RTErrorString('權限不足');
        }
    }

    /**
     * Get Views
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getViews(Request $request)
    {
        $user = $request->user();

        return $this->repository_order->seen($user->id);
    }

    /**
     * Get Owner Evaluations
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showEvaluations(Request $request, $id)
    {
        $booth = $this->repository->getById($id); RTIfEntityNotFound($booth);

        $data = [];
        foreach ($booth->orders as $order) {
            if ($order->owner_evaluation) {
                array_push($data, [
                    'user_id'     => $order->owner_evaluation->user_id,
                    'score'       => $order->owner_evaluation->score,
                    'description' => $order->owner_evaluation->description,
                    'created_at'  => $order->owner_evaluation->created_at
                ]);
            }
        }

        return response()->json($data);
    }

    /**
     * Evaluate the specified resource.
     *
     * @param  App\Models\System\Forms\EvaluationFormRequest
     * @param  int  $id
     * @param  int  $order_id
     * @return \Illuminate\Http\Response
     */
    public function evaluate(EvaluationFormRequest $request, $id, $order_id)
    {
        $order = $this->repository_order->getById($order_id); RTIfEntityNotFound($order);

        if (empty($order)) {
            RTErrorString('尚未交易');
        } else {
            if (!$order->can_evaluate)
                RTErrorString('賣家尚未確認');
            if ($order->is_close)
                RTErrorString('已超過評價期限');
        }

        $user = $request->user();

        $evaluation = $this->repository_evaluation->firstOrCreate(['user_id'     => $user->id,
                                                                   'host_type'   => 'App\Models\Booth\Entities\BoothOrder',
                                                                   'host_id'     => $order->id,
                                                                   'score'       => $request->score,
                                                                   'description' => $request->description]);
        if ($order->booth->user_id == $user->id)
            $order->update(['customer_evaluation_id' => $evaluation->id]);
        else
            $order->update(['owner_evaluation_id' => $evaluation->id]);

        return response()->json(['success' => true]);
    }
}
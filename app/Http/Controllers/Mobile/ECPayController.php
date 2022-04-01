<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\System\Repositories\PointRecordRepository;
use TsaiYiHua\ECPay\Checkout;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;

class ECPayController extends Controller
{
    private $checkout;
    private $repository;

    public function __construct(Checkout $checkout, PointRecordRepository $repository)
    {
        $this->checkout = $checkout;
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $api_token = $request->api_token;
        $imei      = $request->imei;

        return view('selectPoint', compact('api_token', 'imei'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pay(Request $request)
    {
        $user = $request->user();
        $orderID = time();
        $PaymentMethod = explode('_', $request->point)[2];
        $TotalAmount = explode('_', $request->point)[1];
        $point       = explode('_', $request->point)[0];

        $formData = ['OrderId'         => $orderID,
                     'ItemName'        => '點數儲值',
                     'ItemDescription' => $request->point .'點',
                     'TotalAmount'     => (int) $TotalAmount,
                     'PaymentMethod'   => $PaymentMethod,
                     'ReturnURL'       => route('ecpay.return')];

        $this->repository->save(['category'     => 'ECPay',
                                 'user_id'      => $user->id,
                                 'point'        => (int) $point,
                                 'orderID'      => $orderID,
                                 'formData'     => $formData,
                                 'is_confirmed' => 0]);
        
        // 同步會員點數至 Firebase
        $realtimeDatabase = (new Factory)
            ->withServiceAccount(base_path().'/shrimp-king-firebase-adminsdk-wvabh-6b8f7bb223.json')
            ->withDatabaseUri('https://shrimp-king.firebaseio.com/')
            ->createDatabase();

        $updates = [
            "member/$user->firebase_uid/point" => $user->point,
        ];
        $realtimeDatabase->getReference()->update($updates);

        return $this->checkout->setPostData($formData)->send();
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function notifyUrl(Request $request)
    {
        $record = $this->repository->getByOrderID($request->MerchantTradeNo); RTIfEntityNotFound($record);

        if ($request->RtnCode == "1") {
            $record->update(['is_confirmed' => 1, 'returnData' => $request->all()]);
            $record->user->update(['point' => $record->user->point + $record->point]);

        } else {
            $record->update(['returnData' => $request->all()]);
            $record->delete();
        }

        return 1;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function returnUrl(Request $request)
    {
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
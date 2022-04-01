<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FishingTackleShop\Forms\FishingTackleShopFormRequest;
use App\Models\FishingTackleShop\Repositories\FishingTackleShopRepository;
use App\Models\System\Forms\BusinessHourForm;
use App\Models\System\Repositories\BusinessHourRepository;
use App\Models\System\Services\CrawlerService;

class FishingTackleShopController extends Controller
{
    private $repository;
    private $repository_businessHour;
    private $form_business;
    private $service_crawler;

    public function __construct(
        FishingTackleShopRepository $repository,
        BusinessHourRepository $repository_businessHour,
        BusinessHourForm $form_business,
        CrawlerService $service_crawler
    ) {
        $this->repository = $repository;
        $this->repository_businessHour = $repository_businessHour;
        $this->form_business   = $form_business;
        $this->service_crawler = $service_crawler;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = $this->repository->list();

        return view('fishing_tackle_shops.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('fishing_tackle_shops.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->service_crawler->fetchGoogleMapJson($request->url);
    	$this->repository->whereLocation($data['location_lat'], $data['location_lng'])
    	                 ->updateOrCreate($data);

        return redirect('fishingTackleShop');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $record = $this->repository->getById($id); RTIfEntityNotFound($record);
        $record = ['id'             => $record->id,
                   'name'           => $record->name,
                   'address'        => $record->address,
                   'location_lat'   => $record->location_lat,
                   'location_lng'   => $record->location_lng,
                   'phone'          => $record->phone,
                   'is_close'       => $record->is_close,
                   'selected'       => $this->repository_businessHour->getDaysByFishingTackleShop($id),
                   'bussiness_hour' => $this->repository_businessHour->listByFishingTackleShop($id)];

        return view('fishing_tackle_shops.edit', compact('record'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\FishingTackleShop\Forms\FishingTackleShopRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(FishingTackleShopFormRequest $request, $id)
    {
        \DB::beginTransaction();
            try {
                $data = $request->validated();
                $record = $this->repository->getById($id); RTIfEntityNotFound($record);
                $record->update($data);

                $original = $this->repository_businessHour->getDaysByFishingTackleShop($id);
                $unSelected = $original;
                if (isset($data['day'])){
                    $unSelected = array_diff($original, $data['day']);
                    foreach ($data['day'] as $day){
                        $data2 = $this->form_business->verify(['fishing_tackle_shop_id' => $id,
                                                               'day'                    => $day,
                                                               'begin_at'               => $data['begin_at'][$day],
                                                               'end_at'                 => $data['end_at'][$day]]);
                        $this->repository_businessHour->updateOrCreate($data2);
                    }
                }
                $this->repository_businessHour->deleteByFishingTackleShop($id, $unSelected);

                \DB::commit();
            } catch (\Exception $e){
                if (!app()->environment('production')) dd($e);
                \DB::rollback();
            }

        return redirect('fishingTackleShop');
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

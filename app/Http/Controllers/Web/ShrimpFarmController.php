<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShrimpFarm\Forms\ShrimpFarmFormRequest;
use App\Models\ShrimpFarm\Repositories\ShrimpFarmRepository;
use App\Models\ShrimpFarm\Repositories\ShrimpFarmEventRepository;
use App\Models\System\Forms\BusinessHourForm;
use App\Models\System\Repositories\BusinessHourRepository;
use App\Models\System\Repositories\EvaluationRepository;
use App\Models\System\Services\CrawlerService;

class ShrimpFarmController extends Controller
{
    private $repository;
    private $repository_event;
    private $repository_businessHour;
    private $repository_evaluation;
    private $form_business;
    private $service_crawler;

    public function __construct(
        ShrimpFarmRepository $repository,
        ShrimpFarmEventRepository $repository_event,
        BusinessHourRepository $repository_businessHour,
        EvaluationRepository $repository_evaluation,
        BusinessHourForm $form_business,
        CrawlerService $service_crawler
    ) {
        $this->repository              = $repository;
        $this->repository_event        = $repository_event;
        $this->repository_businessHour = $repository_businessHour;
        $this->repository_evaluation   = $repository_evaluation;
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
        $records = $this->repository->listForWeb();

        return view('shrimp_farms.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('shrimp_farms.create');
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

        return redirect('shrimpFarm');
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
                   'content'        => $record->content,
                   'news'           => $record->news,
                   'can_push'       => $record->can_push ? 1 : 0,
                   'is_close'       => $record->is_close ? 1 : 0,
                   'selected'       => $this->repository_businessHour->getDaysByShrimpFarm($id),
                   'bussiness_hour' => $this->repository_businessHour->listByShrimpFarm($id)];

        return view('shrimp_farms.edit', compact('record'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\ShrimpFarm\Forms\ShrimpFarmFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(ShrimpFarmFormRequest $request, $id)
    {
        \DB::beginTransaction();
            try {
                $data = $request->validated();
                $record = $this->repository->getById($id); RTIfEntityNotFound($record);
                $record->update($data);

                $original = $this->repository_businessHour->getDaysByShrimpFarm($id);
                $unSelected = $original;
                if (isset($data['day'])){
                    $unSelected = array_diff($original, $data['day']);
                    foreach ($data['day'] as $day){
                        $data2 = $this->form_business->verify(['shrimp_farm_id' => $id,
                                                               'day'            => $day,
                                                               'begin_at'       => $data['begin_at'][$day],
                                                               'end_at'         => $data['end_at'][$day]]);
                        $this->repository_businessHour->updateOrCreate($data2);
                    }
                }
                $this->repository_businessHour->deleteByShrimpFarm($id, $unSelected);

                \DB::commit();
            } catch (\Exception $e){
                \DB::rollback();
            }

        return redirect('shrimpFarm');
    }

    /**
     * Remove all evaluations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resetEvaluation(Request $request)
    {
        $this->repository_evaluation->delete('App\Models\ShrimpFarm\Entities\ShrimpFarm', $request->id);

        return 1;
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

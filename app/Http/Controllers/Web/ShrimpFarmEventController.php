<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShrimpFarm\Forms\ShrimpFarmEventFormRequest;
use App\Models\ShrimpFarm\Repositories\ShrimpFarmRepository;
use App\Models\ShrimpFarm\Repositories\ShrimpFarmEventRepository;
use App\Models\System\Services\CrawlerService;
use App\Models\System\Services\FCMService;
use App\Models\Profile\Repositories\UserRepository;
use App\Notifications\SomeNotification;
use Carbon\Carbon;

class ShrimpFarmEventController extends Controller
{
    private $repository;
    private $repository_event;
    private $repository_user;
    private $service_crawler;
    private $service_fcm;

    public function __construct(
        ShrimpFarmRepository $repository,
        ShrimpFarmEventRepository $repository_event,
        UserRepository $repository_user,
        CrawlerService $service_crawler,
        FCMService $service_fcm
    ) {
        $this->repository       = $repository;
        $this->repository_event = $repository_event;
        $this->repository_user  = $repository_user;
        $this->service_crawler = $service_crawler;
        $this->service_fcm = $service_fcm;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = $this->repository_event->listForWeb();

        return view('shrimp_farms_events.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $farms = $this->repository->listForWeb(0);
        $options = [];
        $selected = null;
        if (is_null($id)){
            $options[] = ['value' => null,
                          'text' => '請選擇釣蝦場'];
        }else{
        	$selected = $id;
        }
        foreach ($farms as $farm){
            array_push($options, ['value' => $farm['id'],
                                  'text'  => $farm['id'] .' - '. $farm['name']]);
        }

        return view('shrimp_farms_events.create', compact('options', 'selected'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->service_crawler->fetchFacebookPage($request->url);
        $record = $this->repository->getById($request->shrimp_farm_id); RTIfEntityNotFound($record);
        if ($record->is_close == 1)
        	RTErrorString('釣蝦場已不再營業');

        $data['shrimp_farm_id'] = $record->id;
        $this->repository_event->save($data);

        return redirect('shrimpFarmEvent');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = $this->repository_event->getById($id); RTIfEntityNotFound($event);
        if (isset($event->end_at) && $event->end_at->lt(Carbon::now()))
        	RTErrorString('活動已過期');

        $farms = $this->repository->listForWeb(0);
        $options = [];
        foreach ($farms as $farm){
            array_push($options, ['value' => $farm['id'],
                                  'text'  => $farm['id'] .' - '. $farm['name']]);
        }

        $images = '';
        $imgs = $event->images;
        foreach ($imgs as $key=>$img){
            $images .= $img;
            if ($key <= count($imgs)-1) $images .= "\n";
        }
        $record = ['id'       => $event->id,
                   'selected' => $event->shrimp_farm_id,
                   'options'  => $options,
                   'name'     => $event->shrimpFarm->name,
                   'content'  => $event->content,
                   'images'   => $images,
                   'end_at'   => $event->end_at ? $event->end_at->format('Y-m-d H:i:s') : date('Y-m-d').' 23:59:59'];

        return view('shrimp_farms_events.edit', compact('record'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\ShrimpFarm\Forms\ShrimpFarmEventFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(ShrimpFarmEventFormRequest $request, $id)
    {
        $data = $request->validated();
        if (empty($data['images'])){
        	$data['images'] = [];
        }else{
            $data['images'] = str_replace(' ', '', $data['images']);
            $data['images'] = array_map('trim', explode("\n", $data['images']));
        }
        $record = $this->repository_event->getById($id); RTIfEntityNotFound($record);
        $record->update($data);

        if (!is_null($record->end_at) && Carbon::now()->lt($record->end_at) && $record->shrimpFarm->can_push){
            $data = ['can_push_shrimp_event' => 1];
            $input_distance = $this->repository_user->listWithinMaxDistance($record->shrimpFarm->location_lat, $record->shrimpFarm->location_lng, env('PUSH_DISTANCE'), $data);
            $input_liked = $this->repository_user->listWithLikeFarm($record->shrimp_farm_id);

            $input_array = array_merge($input_distance, $input_liked);
            $input_array = array_unique($input_array);
            $records = array_chunk($input_array, 500);
            foreach ($records as $tokens) {
	            $response = $this->service_fcm->send2Devices($tokens, '蝦場活動', '['.$record->shrimpFarm->name.']發佈了新的活動', ['id' => $record->shrimpFarm->id, 'name' => $record->shrimpFarm->name, 'type' => 1, 'click_action' => 'FLUTTER_NOTIFICATION_CLICK', 'channel_id' => 'shrimpking']);
	            \Log::info($response->getBody()->getContents());
            }
            //var_dump($response->getStatusCode());
            //var_dump($response->getBody()->getContents());
        }

        return redirect('shrimpFarmEvent');
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

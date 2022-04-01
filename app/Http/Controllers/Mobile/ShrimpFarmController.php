<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Profile\Entities\User;
use App\Models\ShrimpFarm\Forms\ShrimpFarmFormRequest;
use App\Models\ShrimpFarm\Forms\ShrimpFarmFormSearchRequest;
use App\Models\System\Forms\EvaluationFormRequest;
use App\Models\Profile\Forms\ProfileLikeFarmFormRequest;
use App\Models\ShrimpFarm\Repositories\ShrimpFarmRepository;
use App\Models\ShrimpFarm\Repositories\ShrimpFarmEventRepository;
use App\Models\System\Repositories\EvaluationRepository;
use App\Models\Profile\Repositories\ProfileLikeFarmRepository;
use Carbon\Carbon;

class ShrimpFarmController extends Controller
{
    private $repository;
    private $repository_event;
    private $repository_evaluation;
    private $repository_likes;

    public function __construct(ShrimpFarmRepository $repository,
                                ShrimpFarmEventRepository $repository_event,
                                EvaluationRepository $repository_evaluation,
                                ProfileLikeFarmRepository $repository_likes)
    {
        $this->repository            = $repository;
        $this->repository_event      = $repository_event;
        $this->repository_evaluation = $repository_evaluation;
        $this->repository_likes      = $repository_likes;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  App\Models\ShrimpFarm\Forms\ShrimpFarmFormSearchRequest
     * @return \Illuminate\Http\Response
     */
    public function index(ShrimpFarmFormSearchRequest $request)
    {
        return $this->repository->listForMobile($request->type, $request->location_lat, $request->location_lng);
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
    public function store(Request $request)
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
        $record = $this->repository->getById($id); RTIfEntityNotFound($record);

    	if (empty($request->api_token) || empty($request->imei)) {
    		$user = null;
    	} else {
            $user = User::where('api_token', $request->api_token)
                        ->where('imei', $request->imei)
                        ->first();
    	}

        $evaluations = [];
        foreach($record->evaluations as $evaluation){
            $evaluations[] = ['user_id'     => $evaluation->user_id,
                              'nickname'    => $evaluation->user->nickname,
                              'score'       => $evaluation->score,
                              'description' => $evaluation->description,
                              'created_at'  => $evaluation->created_at ? $evaluation->created_at->format('Y-m-d H:i:s') : NULL];
        }

        $event = $record->events->where('end_at', ">", Carbon::now())
                                ->last();

        return response()->json(['id'          => $record->id,
                                 'name'        => $record->name,
                                 'address'     => $record->address,
                                 'phone'       => $record->phone,
                                 'content'     => $record->content ?? "",
                                 'news'        => $record->news ?? "",
                                 'evaluations' => $evaluations,
                                 'event'       => empty($event) ? null : ['content' => $event->content,
                                                                          'images'  => $event->images,
                                                                          'end_at'  => $event->end_at ? $event->end_at->format('Y-m-d H:i:s') : NULL],
                                 'liked'       => $user ? $record->isLikedByUser($user->id) : null]);
    }

    /**
     * Evaluate the specified resource.
     *
     * @param  App\Models\System\Forms\EvaluationFormRequest
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function evaluate(EvaluationFormRequest $request, $id)
    {
        $farm = $this->repository->getById($id); RTIfEntityNotFound($farm);
        $user = $request->user();

    	$record = $this->repository_evaluation->last($user->id, 'App\Models\ShrimpFarm\Entities\ShrimpFarm', $farm->id);
    	if (!empty($record) && $record->created_at->gt(Carbon::now()->subhours(22)))
            RTErrorString('每次評價須間隔 22 小時。');

    	$this->repository_evaluation->save(['user_id'     => $user->id,
                                            'host_type'   => 'App\Models\ShrimpFarm\Entities\ShrimpFarm',
                                            'host_id'     => $farm->id,
                                            'score'       => $request->score,
                                            'description' => $request->description]);

        return response()->json(['success' => true]);
    }

    /**
     * Like the specified resource.
     *
     * @param  App\Models\Profile\Forms\ProfileLikeFarmFormRequest
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function like(ProfileLikeFarmFormRequest $request, $id)
    {
        $farm = $this->repository->getById($id); RTIfEntityNotFound($farm);
        $user = $request->user();

        if ($request->action == 'like')       $this->repository_likes->like($user->id, $farm->id);
        elseif ($request->action == 'unlike') $this->repository_likes->unLike($user->id, $farm->id);

        return response()->json(['success' => true]);
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
     * @param  \App\Models\ShrimpFarm\Forms\ShrimpFarmFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(ShrimpFarmFormRequest $request, $id)
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
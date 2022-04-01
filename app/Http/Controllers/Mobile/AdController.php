<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Profile\Entities\User;
use App\Models\Ad\Forms\AdFormSearchRequest;
use App\Models\Ad\Repositories\AdRepository;

class AdController extends Controller
{
    private $repository;

    public function __construct(AdRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  App\Models\Ad\Forms\AdFormSearchRequest
     * @return \Illuminate\Http\Response
     */
    public function index(AdFormSearchRequest $request)
    {
    	if (empty($request->api_token) || empty($request->imei)) {
    		$user_id = null;
    	} else {
            $user = User::where('api_token', $request->api_token)
                        ->where('imei', $request->imei)
                        ->first();

    	    $user_id = ($user && $request->type == 'self') ? $user->id : null;
    	}

        return $this->repository->search($user_id, $request->category, $request->location_lat, $request->location_lng);
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
        //
    }
}
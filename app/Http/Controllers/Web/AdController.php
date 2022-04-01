<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ad\Forms\AdFormRequest;
use App\Models\Ad\Repositories\AdRepository;
use App\Models\ShrimpFarm\Repositories\ShrimpFarmRepository;
use App\Models\FishingTackleShop\Repositories\FishingTackleShopRepository;

class AdController extends Controller
{
    private $repository;
    private $repository_farm;
    private $repository_shop;

    public function __construct(
        AdRepository $repository,
        ShrimpFarmRepository $repository_farm,
        FishingTackleShopRepository $repository_shop
    ) {
        $this->repository = $repository;
        $this->repository_farm = $repository_farm;
        $this->repository_shop = $repository_shop;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = $this->repository->list();

        return view('ads.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $farms = $this->repository_farm->listForWeb(0);
        $shops = $this->repository_shop->list(0);

        return view('ads.create', compact('farms', 'shops'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Ad\Forms\AdFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdFormRequest $request)
    {
        $data = $request->validated();
        $this->repository->save($data);

        return redirect('ad');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $farms = $this->repository_farm->listForWeb(0);
        $shops = $this->repository_shop->list(0);

        $record = $this->repository->getById($id); RTIfEntityNotFound($record);
        $record = ['id'           => $record->id,
                   'category'     => $record->category,
                   'name'         => $record->name,
                   'url'          => $record->url,
                   'image_type'   => $record->image_type,
                   'image'        => $record->image,
                   'height'       => $record->height,
                   'weight'       => $record->weight,
                   'location_lat' => $record->location_lat,
                   'location_lng' => $record->location_lng,
                   'shopee'       => $record->shopee,
                   'fb_group'     => $record->fb_group,
                   'fb_page'      => $record->fb_page,
                   'ig'           => $record->ig,
                   'youtube'      => $record->youtube,
                   'sales_farm'   => $record->sales_farm,
                   'sales_shop'   => $record->sales_shop,
                   'is_enabled'   => $record->is_enabled ? 1 : 0];

        return view('ads.edit', compact('farms', 'shops', 'record'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Ad\Forms\AdFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(AdFormRequest $request, $id)
    {
        $data = $request->validated();
        $record = $this->repository->getById($id); RTIfEntityNotFound($record);
        $record->update($data);

        return redirect('ad');
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
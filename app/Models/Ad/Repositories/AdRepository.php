<?php

namespace App\Models\Ad\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\Ad\Entities\Ad;
use App\Models\ShrimpFarm\Entities\ShrimpFarm;
use App\Models\FishingTackleShop\Entities\FishingTackleShop;

class AdRepository extends EloquentRepository
{
    protected $model;

    public function __construct(Ad $model)
    {
        $this->model = $model;
    }

    public function pack($records)
    {
        $list = [];
        foreach($records as $record){
            $sales = [];
            if ($record->sales_farm) {
                foreach ($record->sales_farm as $item) {
                    $farm = ShrimpFarm::find($item);
                    if ($farm && $farm->is_close == 0) {
                        array_push($sales, ['name'         => $farm->name,
                                            'address'      => $farm->address,
                                            'location_lat' => $farm->location_lat,
                                            'location_lng' => $farm->location_lng]);
                    }
                }
            }
            if ($record->sales_shop) {
                foreach ($record->sales_shop as $item) {
                    $shop = FishingTackleShop::find($item);
                    if ($shop && $shop->is_close == 0) {
                        array_push($sales, ['name'         => $shop->name,
                                            'address'      => $shop->address,
                                            'location_lat' => $shop->location_lat,
                                            'location_lng' => $shop->location_lng]);
                    }
                }
            }

            $list[] = ['id'           => $record->id,
                       'category'     => $record->category,
                       'name'         => $record->name,
                       'url'          => $record->url,
                       'image_type'   => $record->image_type,
                       'image'        => $record->image,
                       'height'       => $record->height,
                       'weight'       => $record->weight,
                       'location_lat' => $record->location_lat,
                       'location_lng' => $record->location_lng,
                       'distance'     => round($record->distance, 2) .'KM',
                       'shopee'       => $record->shopee,
                       'fb_group'     => $record->fb_group,
                       'fb_page'      => $record->fb_page,
                       'ig'           => $record->ig,
                       'youtube'      => $record->youtube,
                       'sales'        => $sales,
                       'is_enabled'   => $record->is_enabled];
        }
        return $list;
    }

    public function list()
    {
        $records = $this->model::all();

        return $this->pack($records);
    }

    public function search($user_id, $category, $lat, $lng, $radius = 50)
    {
        $list = [];
        $records = $this->model::when(!is_null($user_id), function ($query) use ($user_id){
                                        return $query->where('user_id', '=', $user_id);
                                    })
                               ->when(!empty($category), function ($query) use ($category){
                                        return $query->where('category', '=', $category);
                                    })
                               ->where('is_enabled', 1)
                               ->isWithinMaxDistance($lat, $lng, $radius)
                               ->orderBy('distance', 'ASC')
                               ->take(500)
                               ->get();

        foreach($records as $record){
            $sales = [];
            if ($record->sales_farm) {
                foreach ($record->sales_farm as $item) {
                    $farm = ShrimpFarm::find($item);
                    if ($farm && $farm->is_close == 0) {
                        array_push($sales, ['name'         => $farm->name,
                                            'address'      => $farm->address,
                                            'location_lat' => $farm->location_lat,
                                            'location_lng' => $farm->location_lng]);
                    }
                }
            }
            if ($record->sales_shop) {
                foreach ($record->sales_shop as $item) {
                    $shop = FishingTackleShop::find($item);
                    if ($shop && $shop->is_close == 0) {
                        array_push($sales, ['name'         => $shop->name,
                                            'address'      => $shop->address,
                                            'location_lat' => $shop->location_lat,
                                            'location_lng' => $shop->location_lng]);
                    }
                }
            }

            $list[] = ['id'           => $record->id,
                       'category'     => $record->category,
                       'name'         => $record->name,
                       'url'          => $record->url,
                       'image_type'   => $record->image_type,
                       'image'        => $record->image,
                       'height'       => $record->height,
                       'weight'       => $record->weight,
                       'location_lat' => $record->location_lat,
                       'location_lng' => $record->location_lng,
                       'distance'     => round($record->distance, 2) .'KM',
                       'shopee'       => $record->shopee,
                       'fb_group'     => $record->fb_group,
                       'fb_page'      => $record->fb_page,
                       'ig'           => $record->ig,
                       'youtube'      => $record->youtube,
                       'sales'        => $sales];
        }

        return $list;
    }
}

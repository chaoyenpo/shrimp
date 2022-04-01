<?php

namespace App\Models\System\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\System\Entities\BusinessHour;

class BusinessHourRepository extends EloquentRepository
{
    protected $model;

    public function __construct(BusinessHour $model)
    {
        $this->model = $model;
    }

    private function listByTarget($from, $id)
    {
        $records = $this->model::when($from == 'ShrimpFarm', function ($query) use ($id){
                                        return $query->where('shrimp_farm_id', '=', $id);
                                    }, function ($query) use ($id){
                                        return $query->where('fishing_tackle_shop_id', '=', $id);
                                    })
                               ->orderBy('day', 'ASC')
                               ->get();

        $list = [];
        for ($i=0; $i<=6; $i++){
            $list[] = ['id'       => NULL,
                       'day'      => $i,
                       'begin_at' => '00:00:00',
                       'end_at'   => '23:59:59'];
        }
        foreach($records as $record){
            $list[$record->day] = ['id'       => $record->id,
                                   'day'      => $record->day,
                                   'begin_at' => $record->begin_at,
                                   'end_at'   => $record->end_at];
        }

        return $list;
    }

    public function listByShrimpFarm($id)
    {
        return self::listByTarget('ShrimpFarm', $id);
    }

    public function listByFishingTackleShop($id)
    {
        return self::listByTarget('FishingTackleShop', $id);
    }

    private function getDaysByTarget($from, $id)
    {
        return $this->model::when($from == 'ShrimpFarm', function ($query) use ($id){
                                        return $query->where('shrimp_farm_id', '=', $id);
                                    }, function ($query) use ($id){
                                        return $query->where('fishing_tackle_shop_id', '=', $id);
                                    })
                           ->orderBy('day', 'ASC')
                           ->pluck('day')
                           ->toArray();
    }

    public function getDaysByShrimpFarm($id)
    {
        return self::getDaysByTarget('ShrimpFarm', $id);
    }

    public function getDaysByFishingTackleShop($id)
    {
        return self::getDaysByTarget('FishingTackleShop', $id);
    }

    public function updateOrCreate($data)
    {
    	return $this->model::where('shrimp_farm_id', $data['shrimp_farm_id'])
    	                   ->where('shrimp_farm_id', $data['shrimp_farm_id'])
    	                   ->where('day', $data['day'])
    	                   ->updateOrCreate($data);
    }

    private function deleteByTarget($from, $id, $unSelected)
    {
        return $this->model::when($from == 'ShrimpFarm', function ($query) use ($id){
                                        return $query->where('shrimp_farm_id', '=', $id);
                                    }, function ($query) use ($id){
                                        return $query->where('fishing_tackle_shop_id', '=', $id);
                                    })
                           ->whereIn('day', $unSelected)
                           ->delete();
    }

    public function deleteByShrimpFarm($id, $unSelected)
    {
        return self::deleteByTarget('ShrimpFarm', $id, $unSelected);
    }

    public function deleteByFishingTackleShop($id, $unSelected)
    {
        return self::deleteByTarget('FishingTackleShop', $id, $unSelected);
    }
}

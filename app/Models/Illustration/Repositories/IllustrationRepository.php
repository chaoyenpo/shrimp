<?php

namespace App\Models\Illustration\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\Illustration\Entities\Illustration;

class IllustrationRepository extends EloquentRepository
{
    protected $model;

    public function __construct(Illustration $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        $list = [];
        $records = $this->model::orderBy('updated_at', 'DESC')
                               ->get();

        foreach($records as $record){
            $data = [
                'id'           => $record->id,
                'name'         => $record->name,
                'steps'        => $record->steps,
                'lengths'      => $record->lengths,
                'data'         => $record->data,
                'photo1'       => $record->photo1,
                'photo2'       => $record->photo2,
                'reviews'      => $record->reviews,
                'price'        => $record->price,
                'manufacturer' => $record->manufacturer,
                'brand'        => $record->brand,
                'youtube'      => $record->youtube,
                'created_at'   => $record->created_at->format('Y-m-d H:i:s'),
                'updated_at'   => $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : NULL];
            array_push($list, $data);
        }

        return $list;
    }
}

<?php

namespace App\Models\System\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\System\Entities\LogNotification;

class LogNotificationRepository extends EloquentRepository
{
    protected $model;

    public function __construct(LogNotification $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        $list = [];
        $records = $this->model::all();

        foreach($records as $record){
            $list = ['id'      => $record->id,
                     'title'   => $record->title,
                     'message' => $record->message];
        }

        return $list;
    }
}

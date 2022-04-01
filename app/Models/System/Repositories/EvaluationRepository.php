<?php

namespace App\Models\System\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\System\Entities\Evaluation;

class EvaluationRepository extends EloquentRepository
{
    protected $model;

    public function __construct(Evaluation $model)
    {
        $this->model = $model;
    }

    public function last($user_id, $host_type, $host_id)
    {
        return $this->model->where('user_id', '=', $user_id)
                           ->where('host_type', '=', $host_type)
                           ->where('host_id', '=', $host_id)
                           ->orderBy('id', 'DESC')
                           ->first();
    }

    public function delete($host_type, $host_id)
    {
        return $this->model->where('host_type', '=', $host_type)
                           ->where('host_id', '=', $host_id)
                           ->delete();
    }

    public function firstOrCreate($data)
    {
        if ( !$this->model::where('user_id', $data['user_id'])
                           ->where('host_type', $data['host_type'])
                           ->where('host_id', $data['host_id'])
                           ->first() ) {
           return $this->model::create($data);
        }
    }
}

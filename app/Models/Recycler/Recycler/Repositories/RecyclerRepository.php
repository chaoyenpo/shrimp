<?php

namespace App\Models\Recycler\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\Recycler\Entities\Recycler;

class RecyclerRepository extends EloquentRepository
{
    protected $model;

    public function __construct(Recycler $model)
    {
        $this->model = $model;
    }
}

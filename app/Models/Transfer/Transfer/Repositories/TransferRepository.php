<?php

namespace App\Models\Transfer\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\Transfer\Entities\Transfer;

class TransferRepository extends EloquentRepository
{
    protected $model;

    public function __construct(Transfer $model)
    {
        $this->model = $model;
    }
}

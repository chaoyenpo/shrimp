<?php

namespace App\Models\Profile\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\Profile\Entities\ProfileLikeFarm;

class ProfileLikeFarmRepository extends EloquentRepository
{
    protected $model;

    public function __construct(ProfileLikeFarm $model)
    {
        $this->model = $model;
    }

    public function like($user_id, $shrimp_farm_id)
    {
        return $this->model->firstOrCreate(['user_id'        => $user_id,
                                            'shrimp_farm_id' => $shrimp_farm_id]);
    }

    public function unLike($user_id, $shrimp_farm_id)
    {
        return $this->model->where('user_id', '=', $user_id)
                           ->where('shrimp_farm_id', '=', $shrimp_farm_id)
                           ->delete();
    }
}

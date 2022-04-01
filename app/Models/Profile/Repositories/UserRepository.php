<?php

namespace App\Models\Profile\Repositories;

use App\Models\Core\EloquentRepository;
use App\Models\Profile\Entities\User;

class UserRepository extends EloquentRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getByPhone($phone)
    {
        return $this->model::where('phone', '=', $phone)
                           ->first();
    }

    public function getByIMEI($imei)
    {
        return $this->model::where('imei', '=', $imei)
                           ->first();
    }

    public function listWithinMaxDistance($lat, $lng, $radius, $data) {
        return $this->model::isWithinMaxDistance($lat, $lng, $radius, $data)
                           ->pluck('device_token')
                           ->toArray();
    }

    public function listWithLikeFarm($shrimp_farm_id)
    {
        return $this->model->whereHas('likeFarms', function($query) use ($shrimp_farm_id) {
                                $query->where('shrimp_farm_id', $shrimp_farm_id);
                            })
                           ->pluck('device_token')
                           ->toArray();
    }

    public function loginUsingFirebaseUid($firebase_uid) {
        return $this->model::where('firebase_uid', '=', $firebase_uid)
                           ->first();
    }

    public function logoutUsingIMEI($imei) {
        return $this->model::where('imei', '=', $imei)
                           ->first();
    }
}

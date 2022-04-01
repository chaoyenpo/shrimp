<?php

namespace App\Models\Profile\Forms;

use Illuminate\Support\Facades\Validator;
use App\Models\Profile\Entities\User;

class UserForm
{
    public function checkExistEmail($imei, $email)
    {
        return User::when($imei, function ($query, $imei) {
                        return $query->where('imei', '<>', $imei);
                    })
                   ->where('email', '=', $email)
                   ->count() > 0 ? true : false;
    }

    public function checkExistPhone($imei, $phone)
    {
        return User::when($imei, function ($query, $imei) {
                        return $query->where('imei', '<>', $imei);
                    })
                   ->where('phone', '=', $phone)
                   ->count() > 0 ? true : false;
    }
}

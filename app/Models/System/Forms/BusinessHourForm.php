<?php

namespace App\Models\System\Forms;

use Illuminate\Support\Facades\Validator;

class BusinessHourForm
{
    public function verify($data)
    {
        $validator = Validator::make($data, [
            'shrimp_farm_id'         => ['required_without:fishing_tackle_shop_id','integer','min:1','exists:shrimp_farms,id'],
            'fishing_tackle_shop_id' => ['required_without:shrimp_farm_id','integer','min:1','exists:fishing_tackle_shops,id'],
            'day'                    => 'required|integer|max:1',
            'begin_at'               => 'required|string|min:8|max:8',
            'end_at'                 => 'required|string|min:8|max:8'
        ],[
            'shrimp_farm_id.required_without'         => '此欄位為必填欄位',
            'shrimp_farm_id.integer'                  => '此欄位必須為整數',
            'shrimp_farm_id.min'                      => '輸入值過小',
            'shrimp_farm_id.exists'                   => '找不到對應的釣蝦場',
            'fishing_tackle_shop_id.required_without' => '此欄位為必填欄位',
            'fishing_tackle_shop_id.integer'          => '此欄位必須為整數',
            'fishing_tackle_shop_id.min'              => '輸入值過小',
            'fishing_tackle_shop_id.exists'           => '找不到對應的釣具店',
            'day.required'                            => '此欄位為必填欄位',
            'day.integer'                             => '此欄位必須為整數',
            'day.max'                                 => '輸入長度過長',
            'begin_at.required'                       => '此欄位為必填欄位',
            'begin_at.min'                            => '輸入長度過短',
            'begin_at.max'                            => '輸入長度過長',
            'end_at.required'                         => '此欄位為必填欄位',
            'end_at.min'                              => '輸入長度過短',
            'end_at.max'                              => '輸入長度過長'
        ]);

        if ($validator->fails())
            RTErrorsIfExist(200, $validator->errors());
        else
            return ['shrimp_farm_id'         => $data['shrimp_farm_id'] ?? NULL,
                    'fishing_tackle_shop_id' => $data['fishing_tackle_shop_id'] ?? NULL,
                    'day'                    => $data['day'],
                    'begin_at'               => $data['begin_at'],
                    'end_at'                 => $data['end_at']];
    }
}

<?php

namespace App\Models\Booth\Forms;

use Illuminate\Foundation\Http\FormRequest;

class BoothFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category'     => 'required|numeric|min:1|max:2',
            'commodity'    => 'required',
            'weight'       => 'required',
            'price'        => 'required',
            'status'       => 'required',
            'note'         => '',
            'address'      => '',
            'location_lat' => 'numeric|min:-90|max:90',
            'location_lng' => 'numeric|min:-180|max:180',
            'begin_at'     => 'required|string|date|date_format:Y-m-d H:i:s|before:end_at',
            'end_at'       => 'date|date_format:Y-m-d H:i:s|after:begin_at',
            'is_enabled'   => 'boolean'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'category.required'    => '此欄位為必填欄位',
            'category.numeric'     => '格式不正確',
            'category.min'         => '輸入值過小',
            'category.max'         => '輸入值過大',
            'commodity.required'   => '此欄位為必填欄位',
            'weight.required'      => '此欄位為必填欄位',
            'price.required'       => '此欄位為必填欄位',
            'status.required'      => '此欄位為必填欄位',
            'location_lat.numeric' => '格式不正確',
            'location_lat.min'     => '輸入值過小',
            'location_lat.max'     => '輸入值過大',
            'location_lng.numeric' => '格式不正確',
            'location_lng.min'     => '輸入值過小',
            'location_lng.max'     => '輸入值過大',
            'begin_at.required'    => '此欄位為必填欄位',
            'begin_at.date'        => '此欄位為 Timestamp 欄位',
            'begin_at.date_format' => '格式應為：Y-m-d H:i:s',
            'begin_at.before'      => '時間邏輯不正確',
            'end_at.required'      => '此欄位為必填欄位',
            'end_at.date'          => '此欄位為 Timestamp 欄位',
            'end_at.date_format'   => '格式應為：Y-m-d H:i:s',
            'end_at.after'         => '時間邏輯不正確',
            'is_enabled.boolean'   => '格式不正確'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
        	$data = $validator->getData();
        	$count = substr_count($data['commodity'], ',');
        	if ($count > 2)
                $validator->errors()->add('commodity', '超過數量上限');
            elseif ($count != substr_count($data['weight'], ','))
                $validator->errors()->add('weight', '數量比對錯誤');
            elseif ($count != substr_count($data['price'], ','))
                $validator->errors()->add('price', '數量比對錯誤');

            if (!in_array($data['status'], ['活跳跳', '冷凍'])){
                $validator->errors()->add('status', '參數錯誤');
            }
        });
    }

    protected function failedValidation($validator){
        RTErrorsIfExist(200, $validator->errors()); 
    }
}

<?php

namespace App\Models\Game\Forms;

use Illuminate\Foundation\Http\FormRequest;

class GameFormRequest extends FormRequest
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
        $rules = [
            'shrimp_farm_id'    => ['required','integer','min:1','exists:shrimp_farms,id'],
            'identifier'        => ['required','string'],
            'name'              => 'required|string',
            'location_catrgory' => 'required|string',
            'people_num'        => 'required|numeric|min:1|max:255',
            'host_quota'        => 'required|numeric|min:0|max:999',
            'note'              => 'nullable|string',
            'community'         => 'nullable|string',
            'sponsor'           => 'nullable|string',
            'bait'              => 'nullable|string',
            'status'            => 'nullable|string',
            'begin_at'          => 'nullable|string|date|date_format:Y-m-d',
            'start_at'          => 'nullable|string',
            'mode' => 'required|string',
            'bonus' => 'required|string',
            'fee' => 'required',
            'bet' => 'required',
            'type' => 'required',
        ];

        $request = \Illuminate\Support\Facades\Request::instance();
        if ($request->isMethod('put') && isset($request->id)) {
            $rules = array_merge($rules, ['id' => ['required','integer','min:1','exists:games,id']]);
            $rules['identifier'] = array_merge($rules['identifier'], ['unique:games,identifier,'.$request->id.',id']);
        } else {
            $rules['identifier'] = array_merge($rules['identifier'], ['unique:games,identifier,id']);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'shrimp_farm_id.required'    => '此欄位為必填欄位',
            'shrimp_farm_id.integer'     => '此欄位必須為整數',
            'shrimp_farm_id.min'         => '輸入值過小',
            'shrimp_farm_id.exists'      => '找不到對應的釣蝦場',
            'identifier.required'        => '此欄位為必填欄位',
            'identifier.string'          => '此欄位必為字串',
            'identifier.unique'          => '此欄位不可重複',
            'name.required'              => '此欄位為必填欄位',
            'name.string'                => '此欄位必為字串',
            'location_catrgory.required' => '此欄位為必填欄位',
            'location_catrgory.string'   => '此欄位必為字串',
            'people_num.required'        => '此欄位為必填欄位',
            'people_num.numeric'         => '此欄位必為數字',
            'people_num.min'             => '輸入值過小',
            'people_num.max'             => '輸入值過大',
            'host_quota.required'        => '此欄位為必填欄位',
            'host_quota.numeric'         => '此欄位必為數字',
            'host_quota.min'             => '輸入值過小',
            'host_quota.max'             => '輸入值過大',
            'note.string'                => '此欄位必為字串',
            'community.string'           => '此欄位必為字串',
            'sponsor.string'             => '此欄位必為字串',
            'bait.string'                => '此欄位必為字串',
            'status.string'              => '此欄位必為字串',
            'begin_at.string'            => '此欄位必為字串',
            'begin_at.date'              => '此欄位為 Timestamp 欄位',
            'begin_at.date_format'       => '格式應為：Y-m-d'
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
            if (isset($data['people_num']) && isset($data['host_quota']) && $data['people_num'] < $data['host_quota'])
                $validator->errors()->add('host_quota', '邏輯錯誤');
        });
    }

    protected function failedValidation($validator){
        RTErrorsIfExist(200, $validator->errors()); 
    }
}

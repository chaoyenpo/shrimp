<?php

namespace App\Models\System\Forms;

use Illuminate\Foundation\Http\FormRequest;

class PointRecordFormRequest extends FormRequest
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
            'category' => 'required|string|max:20',
            'user_id'  => ['required','integer','min:1','exists:users,id'],
            'point'    => 'required|integer'
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
            'category.required' => '此欄位為必填欄位',
            'category.max'      => '輸入長度過長',
            'user_id.required'  => '此欄位為必填欄位',
            'user_id.integer'   => '此欄位必須為整數',
            'user_id.min'       => '輸入值過小',
            'user_id.exists'    => '找不到對應的會員',
            'point.required'    => '此欄位為必填欄位',
            'point.integer'     => '此欄位必須為整數'
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
    }

    protected function failedValidation($validator){
        RTErrorsIfExist(200, $validator->errors()); 
    }
}

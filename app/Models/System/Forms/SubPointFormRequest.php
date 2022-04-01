<?php

namespace App\Models\System\Forms;

use Illuminate\Foundation\Http\FormRequest;

class SubPointFormRequest extends FormRequest
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
            'point' => 'required|numeric|min:1',
            'mobile'  => 'required','integer','exists:users,phone',
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
            '*.required' => '此欄位為必填欄位',
            '*.numeric' => '此欄位需為數字',
            'last_4.digits'   => '需為四個整數',
            'time.date'       => '需為日期',
            'mobile.exists'    => '找不到對應的會員',
            '*.min'               => '輸入值過小',
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

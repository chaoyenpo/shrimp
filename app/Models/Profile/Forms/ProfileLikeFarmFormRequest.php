<?php

namespace App\Models\Profile\Forms;

use Illuminate\Foundation\Http\FormRequest;

class ProfileLikeFarmFormRequest extends FormRequest
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
            'action' => 'required|string|min:4|max:6'
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
            'action.required' => '此欄位為必填欄位',
            'action.min'      => '輸入長度過短',
            'action.max'      => '輸入長度過長'
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

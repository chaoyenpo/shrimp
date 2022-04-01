<?php

namespace App\Models\Profile\Forms;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Profile\Entities\User;

class UserPushSwitcherFormRequest extends FormRequest
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
            'can_push_booth_1'      => 'boolean',
            'can_push_booth_2'      => 'boolean',
            'can_push_shrimp_event' => 'boolean'
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
            'can_push_booth_1.boolean'      => '格式不正確',
            'can_push_booth_2.boolean'      => '格式不正確',
            'can_push_shrimp_event.boolean' => '格式不正確'
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

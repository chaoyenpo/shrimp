<?php

namespace App\Models\Profile\Forms;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Profile\Entities\User;

class UserFormRequest extends FormRequest
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
            'device_token'          => 'max:255',
            'imei'                  => 'string|max:255',
            'firebase_uid'          => 'string|max:255',
            'nickname'              => 'string|max:20',
            'email'                 => 'email|max:255',
            'photo'                 => 'nullable|url|max:255',
            'note'                  => 'nullable|string',
            'phone'                 => 'max:20',
            'location_lat'          => 'numeric',
            'location_lng'          => 'numeric',
            'is_vendor'             => 'boolean',
            'is_shrimper'           => 'boolean',
            'is_recycler'           => 'boolean',
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
            'device_token.max'              => '輸入長度過長',
            'imei.max'                      => '輸入長度過長',
            'firebase_uid.max'              => '輸入長度過長',
            'nickname.max'                  => '輸入長度過長',
            'email.email'                   => '格式不正確',
            'email.max'                     => '輸入長度過長',
            'photo.url'                     => '格式不正確',
            'photo.max'                     => '輸入長度過長',
            'note.string'                   => '格式不正確',
            'phone.max'                     => '輸入長度過長',
            'location_lat.numeric'          => '格式不正確',
            'location_lng.numeric'          => '格式不正確',
            'is_vendor.boolean'             => '格式不正確',
            'is_shrimper.boolean'           => '格式不正確',
            'is_recycler.boolean'           => '格式不正確'
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
        $request = \Illuminate\Support\Facades\Request::instance();
        $validator->after(function ($validator) use ($request){
        	$data = $validator->getData();
            if ($request->isMethod('post')){
                if (empty($data['firebase_uid'])) $validator->errors()->add('firebase_uid', '此欄位為必填欄位');
                if (empty($data['nickname']))     $validator->errors()->add('nickname', '此欄位為必填欄位');
            }
            if (!empty($data['firebase_uid'])){
                $count = User::where('firebase_uid', '=', $data['firebase_uid'])
                             ->when(!empty($data['id']), function ($query) use ($data){
                                        return $query->where('id', '<>', $data['id']);
                                    })
                             ->count();
                if ($count > 0) $validator->errors()->add('firebase_uid', '重複');
            }

            if (!empty($data['email'])){
                $count = User::where('email', '=', $data['email'])
                             ->when(!empty($data['id']), function ($query) use ($data){
                                        return $query->where('id', '<>', $data['id']);
                                    })
                             ->count();
                if ($count > 0) $validator->errors()->add('email', '重複');
            }

            if (!empty($data['phone'])){
                $count = User::where('phone', '=', $data['phone'])
                             ->when(!empty($data['id']), function ($query) use ($data){
                                        return $query->where('id', '<>', $data['id']);
                                    })
                             ->when($request->user(), function ($query) use ($request){
                                        return $query->where('id', '<>', $request->user()->id);
                                    })
                             ->count();
                if ($count > 0) $validator->errors()->add('phone', '手機號碼已被註冊');
            }
        });
    }

    protected function failedValidation($validator){
        RTErrorsIfExist(200, $validator->errors()); 
    }
}

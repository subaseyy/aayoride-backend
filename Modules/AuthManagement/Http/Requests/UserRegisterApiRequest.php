<?php

namespace Modules\AuthManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterApiRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email|unique:users',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:17|unique:users',
            'password' => 'required|min:8',
            'profile_image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
            'identification_type' => 'in:nid,passport,driving_licence',
            'identification_number' => 'sometimes',
            'identity_images' => 'sometimes|array',
            'identity_images.*' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
            'fcm_token' => 'sometimes',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}

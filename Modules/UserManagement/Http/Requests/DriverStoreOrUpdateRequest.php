<?php

namespace Modules\UserManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->id;
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:17|unique:users,phone,' . $id,
            'password' => !is_null($this->password) ? 'required|min:8' : 'nullable',
            'confirm_password' => [
                Rule::requiredIf(function (){
                    return $this->password != null;
                }),
                'same:password'],
            'profile_image' => [
                Rule::requiredIf(empty($id)),
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:10000'
            ],
            'identification_type' => 'required|in:passport,driving_license,nid',
            'identification_number' => 'required',
            'identity_images' => 'array',
            'other_documents' => 'array',
            'other_documents.*' => [
                Rule::requiredIf(empty($id)),
                'max:10000'
            ],
            'identity_images.*' => [
                Rule::requiredIf(empty($id)),
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:10000'
            ],
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

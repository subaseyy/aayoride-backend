<?php

namespace Modules\BusinessManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LandingCTAStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['required', Rule::in(CTA, CTA_IMAGE)],
            'title' => Rule::requiredIf(function () {
                return $this->input('type') === CTA;
            }),
            'sub_title' => Rule::requiredIf(function () {
                return $this->input('type') === CTA;
            }),
            'play_store_user_download_link' => [
                Rule::requiredIf(function () {
                    return $this->input('type') === CTA;
                }),
                'url'
            ],
            'play_store_driver_download_link' => [
                Rule::requiredIf(function () {
                    return $this->input('type') === CTA;
                }),
                'url'
            ],
            'app_store_user_download_link' => [
                Rule::requiredIf(function () {
                    return $this->input('type') === CTA;
                }),
                'url'
            ],
            'app_store_driver_download_link' => [
                Rule::requiredIf(function () {
                    return $this->input('type') === CTA;
                }),
                'url'
            ],
            'image' => [
                'image',
                'mimes:jpg,png,jpeg',
                'max:5200'
            ],
            'background_image' => [
                'image',
                'mimes:jpg,png,jpeg',
                'max:5200'
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
        return Auth::check();
    }
}

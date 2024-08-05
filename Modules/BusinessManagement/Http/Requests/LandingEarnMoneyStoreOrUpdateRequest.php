<?php

namespace Modules\BusinessManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LandingEarnMoneyStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['required', Rule::in(EARN_MONEY, EARN_MONEY_IMAGE)],
            'title' => Rule::requiredIf(function () {
                return $this->input('type') === EARN_MONEY;
            }),
            'sub_title' => Rule::requiredIf(function () {
                return $this->input('type') === EARN_MONEY;
            }),
            'image' => [
                Rule::requiredIf(function () {
                    return $this->input('type') === EARN_MONEY_IMAGE && $this->has('image');
                }),
                'image',
                'mimes:jpg,png,jpeg',
                'max:5200'
            ]
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

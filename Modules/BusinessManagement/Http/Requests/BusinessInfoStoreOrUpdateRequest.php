<?php

namespace Modules\BusinessManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BusinessInfoStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "business_contact_phone" => "nullable",
            "business_contact_email" => "nullable|email",
            "business_support_phone" => "nullable",
            "business_support_email" => "nullable|email",
            "copyright_text" => "nullable|string",
            "business_name" => "nullable|string",
            "business_address" => "nullable|string",
            "trade_licence_number" => "nullable|string",
            "country_code" => "required",
            "language" => "nullable|array",
            "currency_symbol_position" => 'nullable|in:left,right',
            "currency_decimal_point" => "nullable|integer|gte:0|lt:11",
            "driver_self_registration" => "nullable|string|in:on",
            "driver_verification" => "nullable|string|in:on",
            "website_color" => "nullable|array",
            "text_color" => "nullable|array",
            "header_logo" => "nullable|mimes:png",
            "footer_logo" => "nullable|mimes:png",
            "favicon" => "nullable|mimes:png",
            "preloader" => "nullable|mimes:gif",
            "app_logo" => "nullable|mimes:png",
            "time_zone" => "nullable|string",
            "time_format" => "nullable|string",
            "customer_verification" => "nullable|string|in:on",
            'parcel_weight_unit' => 'required',
            'currency_code' => 'sometimes'
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

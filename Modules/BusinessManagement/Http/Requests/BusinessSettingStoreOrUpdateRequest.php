<?php

namespace Modules\BusinessManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BusinessSettingStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "trip_commission" => "nullable|numeric|gt:0",
            "vat_percent" => "nullable|numeric|gte:0",
            "search_radius" => "nullable|numeric|gt:0",
            "driver_completion_radius" => "nullable|numeric|gt:0",
            "pagination_limit" => "nullable|numeric|gt:0",
            "temporary_block_time" => "nullable|numeric|gt:0",
            "otp_resend_time" => "nullable|numeric|gt:0",
            "maximum_otp_hit" => "nullable|numeric|gt:0",
            "temporary_login_block_time" => "nullable|numeric|gt:0",
            "maximum_login_hit" => "nullable|numeric|gt:0",
            'websocket_url' => 'sometimes',
            'websocket_port' => 'sometimes',
            'bid_on_fare' => 'sometimes',
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

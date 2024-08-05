<?php

namespace Modules\BusinessManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TripFareSettingStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required',
            'idle_fee' => 'required_if:type,trip_fare_settings|gt:0',
            'delay_fee' => 'required_if:type,trip_fare_settings|gt:0',
            'add_intermediate_points' => 'required_if:type,trip_settings|boolean',
            'trip_request_active_time' => 'required_if:type,trip_settings|gt:0|lte:30',
            'trip_push_notification' => 'sometimes',
            'bidding_push_notification' => 'sometimes',
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

<?php

namespace Modules\TripManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GetEstimatedFaresOrNotRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pickup_coordinates' => 'required',
            'destination_coordinates' => 'required',
            'pickup_address' => 'required',
            'destination_address' => 'required',
            'type' => 'required|in:parcel,ride_request',
            'parcel_weight' => 'required_if:type,==,parcel',
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

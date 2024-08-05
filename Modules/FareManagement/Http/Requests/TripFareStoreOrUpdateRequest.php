<?php

namespace Modules\FareManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TripFareStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'base_fare' => 'nullable|gt:0',
            'base_fare_per_km' => 'nullable|gt:0',
            'waiting_fee' => 'nullable|gte:0',
            'cancellation_fee' => 'nullable|gte:0',
            'min_cancellation_fee' => 'nullable|gte:0',
            'idle_fee' => 'nullable|gte:0',
            'trip_delay_fee' => 'nullable|gte:0',
            'penalty_fee_for_cancel' => 'nullable|gte:0',
            'fee_add_to_next' => 'nullable|gte:0',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }
}

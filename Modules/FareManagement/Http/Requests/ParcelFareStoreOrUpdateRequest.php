<?php

namespace Modules\FareManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ParcelFareStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'parcel_category' => 'required|array',
            'base_fare' => 'required|gt:0',
        ];
    }

    public function messages(): array
    {
        return [
            'parcel_category.required' => 'Must select at least one parcel category'
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

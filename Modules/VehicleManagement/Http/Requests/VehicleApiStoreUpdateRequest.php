<?php

namespace Modules\VehicleManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VehicleApiStoreUpdateRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->id;

        return [
            'brand_id' => 'required',
            'model_id' => 'required',
            'category_id' => 'required',
            'driver_id' => 'required',
            'ownership' => 'required',
            'licence_plate_number' => 'required',
            'licence_expire_date' => 'required|date',
            'vin_number' => 'sometimes',
            'transmission' => 'sometimes',
            'fuel_type' => 'required',
            'upload_documents' => 'required',
        ];
    }

    public function authorize()
    {
        return Auth::check();
    }
}

<?php

namespace Modules\VehicleManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VehicleStoreUpdateRequest extends FormRequest
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
            'brand_id' => 'required',
            'model_id' => 'required',
            'category_id' => 'required',
            'licence_plate_number' => 'required',
            'licence_expire_date' => 'required|date',
            'vin_number' => 'nullable',
            'transmission' => 'nullable',
            'fuel_type' => 'required',
            'ownership' => 'required|in:admin,driver',
            'driver_id' => 'required|unique:vehicles,driver_id,'.$id,
            'upload_documents' => 'array',
            'upload_documents.*' => [
                Rule::requiredIf(empty($id)),
                'mimes:xls,xlsx,pdf,png,jpeg,cvc,csv,jpg',
                'max:10000']
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

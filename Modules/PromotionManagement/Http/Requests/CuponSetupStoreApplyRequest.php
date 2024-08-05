<?php

namespace Modules\PromotionManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class cuponSetupStoreApplyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'coupon_code' => 'required',
            'pickup_coordinates' => 'required',
            'vehicle_category_id' => 'required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}

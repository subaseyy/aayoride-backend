<?php

namespace Modules\VehicleManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\VehicleManagement\Entities\VehicleBrand;

class VehicleModelStoreUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->id;
        $brand_id= $this->request->get('brand_id');
        return [
            'name' => ['required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('vehicle_models', 'name')->where(fn($query) => $query->where('brand_id', $brand_id)->where('id','!=',$id))
            ],
            'brand_id' => ['required',Rule::exists(VehicleBrand::class,'id')],
            'short_desc' => 'required',
            'seat_capacity' => 'numeric|max:99999999|gt:0',
            'maximum_weight' => 'numeric|max:99999999|gt:0',
            'hatch_bag_capacity' => 'numeric|max:99999999|gt:0',
            'engine' => 'required',
            'model_image' => [
                Rule::requiredIf(empty($id)),
                'image',
                'mimes:png',
                'max:5000']
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

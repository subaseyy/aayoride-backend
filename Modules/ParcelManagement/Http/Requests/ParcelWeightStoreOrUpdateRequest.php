<?php

namespace Modules\ParcelManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ParcelWeightStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->id;
        $minWeight = $this->min_weight;
        $maxWeight = $this->max_weight;
        return [
            'min_weight' => [
                Rule::requiredIf(empty($id)),
                'numeric',
                'lte:max_weight',
                'gte:0'
            ],
            'max_weight' => [
                Rule::requiredIf(empty($id)),
                'numeric',
                'gt:min_weight'
            ]
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

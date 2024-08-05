<?php

namespace Modules\ParcelManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ParcelCategoryStoreOrUpdateRequest extends FormRequest
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
            'category_name' => 'required|unique:parcel_categories,name,' . $id,
            'short_desc' => 'required',
            'category_icon' => [
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

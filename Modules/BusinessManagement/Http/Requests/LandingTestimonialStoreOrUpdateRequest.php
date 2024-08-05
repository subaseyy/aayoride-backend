<?php

namespace Modules\BusinessManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LandingTestimonialStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reviewer_name' => 'required',
            'designation' => 'required',
            'rating' => 'numeric|min:0|max:5',
            'review' => 'max:500',
            'reviewer_image' => 'sometimes|image|mimes:jpg,png,jpeg|max:5200'
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

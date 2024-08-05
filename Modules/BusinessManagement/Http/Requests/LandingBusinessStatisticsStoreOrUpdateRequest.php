<?php

namespace Modules\BusinessManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LandingBusinessStatisticsStoreOrUpdateRequest extends FormRequest
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
            'total_download_image' => 'sometimes|image|mimes:jpg,png,jpeg|max:5200',
            'total_download_count' => 'required|string',
            'total_download_content' => 'required|string',
            'complete_ride_image' => 'sometimes|image|mimes:jpg,png,jpeg|max:5200',
            'complete_ride_count' => 'required|string',
            'complete_ride_content' => 'required|string',
            'happy_customer_image' => 'sometimes|image|mimes:jpg,png,jpeg|max:5200',
            'happy_customer_count' => 'required|string',
            'happy_customer_content' => 'required|string',
            'support_image' => 'sometimes|image|mimes:jpg,png,jpeg|max:5200',
            'support_title' => 'required|string',
            'support_content' => 'required|string',
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

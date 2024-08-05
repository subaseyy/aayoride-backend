<?php

namespace Modules\PromotionManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DiscountStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->id;
        $data = $this;
        return [
            'title' => 'required|max:50',
            'short_description' => 'required|max:150',
            'terms_conditions' => 'required|max:400',
            'image' => [
                Rule::requiredIf(empty($id)),
                'image',
                'mimes:png,jpg,jpeg',
                'max:5000'],
            'zone_discount_type'=>'required|array',
            'customer_level_discount_type'=>'required|array',
            'customer_discount_type'=>'required|array',
            'module_discount_type'=>'required|array',
            'discount_amount_type'=>Rule::in(AMOUNT,PERCENTAGE),
            'limit_per_user' => 'required|gt:0',
            'discount_amount' => [
                'required',
                function ($attribute, $value, $fail) use ($data) {
                    $amountType = $data['discount_amount_type'];
                    $minTripAmount = $data['min_trip_amount'];
                    $discountAmount = $data['discount_amount'];
                    if ($amountType === AMOUNT && $value <= 0) {
                        $fail(translate('The discount amount  value must be gather than 0 '));
                    }
                    if ($amountType === PERCENTAGE && $value <= 0) {
                        $fail(translate('The discount percent value must be gather than 0 '));
                    }

                    if ($amountType === PERCENTAGE && $value > 100) {
                        $fail(translate('Discount percent value must be less than 100% '));
                    }
                    if ($amountType !== PERCENTAGE && $discountAmount >= $minTripAmount) {
                        $fail(translate('Discount amount is not equal or more than minimum trip amount'));
                    }
                },
            ],
            'min_trip_amount' => 'required|gt:0',
            'max_discount_amount' => $this->discount_amount_type === PERCENTAGE ? 'required|numeric|gt:0' : '',
            'start_date' => 'required|after_or_equal:today,' . $id,
            'end_date' => 'required|after_or_equal:start_date,' . $id
        ];
    }

    public function messages(): array
    {
        return [
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

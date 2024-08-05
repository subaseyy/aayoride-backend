<?php

namespace Modules\PromotionManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CouponSetupStoreUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $id = $this->id;
        $data = $this;
        $request = $this->request->all();
        return [
            'coupon_title' => 'required|max:50',
            'short_desc' => 'required|max:255',
            'coupon_code' => [
                Rule::requiredIf(empty($id)),
                'max:30',
                'unique:coupon_setups,coupon_code,' . $id
            ],
            'user_id' => [
                Rule::requiredIf(empty($id)),
            ],
            'user_level_id' => [
                Rule::requiredIf(function () use ($id, $request) {
                    return empty($id) && $request['user_id'] == 'all';
                }),
                'string',
            ],
            'limit_same_user' => 'required|gt:0',
            'coupon_type' => 'required',
            'amount_type' => 'required',
            'coupon' => [
                'required',
                function ($attribute, $value, $fail) use ($data) {
                    $amountType = $data['amount_type'];
                    $minTripAmount = $data['minimum_trip_amount'];
                    $couponAmount = $data['coupon'];
                    if ($amountType === 'amount' && $value <= 0) {
                        $fail('The coupon amount  value must be gather than 0 ');
                    }
                    if ($amountType === 'percentage' && $value <= 0) {
                        $fail('The coupon percent value must be gather than 0 ');
                    }

                    if ($amountType === 'percentage' && $value > 100) {
                        $fail('The coupon percent value must be less than 100% ');
                    }
                    if ($amountType !== 'percentage' && $couponAmount >= $minTripAmount) {
                        $fail('Coupon amount is not equal or more than minimum trip amount');
                    }
                },
            ],
            'minimum_trip_amount' => 'required|gt:0',
            'max_coupon_amount' => $data['amount_type'] == 'percentage' ? 'required|numeric|gt:0' : '',
            'start_date' => 'required|after_or_equal:today,' . $id,
            'end_date' => 'required|after_or_equal:start_date,' . $id,
            'coupon_rules' => [
                Rule::requiredIf(empty($id)),
                'in:default,area_wise,vehicle_category_wise,' . $id
            ],
            'categories' => 'required_if:coupon_rules,vehicle_category_wise'
        ];
    }

    public function messages(): array
    {
        return [
            'coupon_code.required_if' => translate('The_coupon_code_is_required'),
            'coupon_code.max' => translate('The_coupon_code_must_not_be_greater_than_30_characters.'),
            'coupon_code.unique' => translate('The_coupon_code_has_already_been_taken.'),
            'coupon_rules.required_if' => translate('Please_select_a_coupon_rule.'),
            'coupon_rules.in' => translate('The_selected_coupon_rule_is_invalid.'),
            'categories.required_if' => translate('Please_select_at_least_one_category_for_vehicle_category_wise_coupon_rule.'),
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

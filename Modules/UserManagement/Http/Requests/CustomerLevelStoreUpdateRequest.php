<?php

namespace Modules\UserManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerLevelStoreUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->id;
        $rewardType = $this->reward_type;
        $minimumRideComplete = (bool)$this->minimum_ride_complete;
        $minimumEarnAmount = (bool)$this->minimum_earn_amount;
        $maximumCancellationRate = (bool)$this->maximum_cancellation_rate;
        $minimumReviewReceive = (bool)$this->minimum_review_receive;
        return [
            'sequence' => [
                Rule::requiredIf(empty($id)),
                'numeric',
                Rule::unique('user_levels', 'sequence')->where('user_type', CUSTOMER)->ignore($id)
            ],
            'name' => [
                Rule::requiredIf(empty($id)),
                Rule::unique('user_levels', 'name')->where('user_type', CUSTOMER)->ignore($id)
            ],
            'reward_type' => [
                Rule::requiredIf(empty($id)),
                'in:no_rewards,wallet,loyalty_points'
            ],
            'minimum_ride_complete' => [
                Rule::requiredIf(function () use ($minimumRideComplete, $minimumEarnAmount, $maximumCancellationRate, $minimumReviewReceive) {
                    return !($minimumRideComplete || $minimumEarnAmount || $maximumCancellationRate || $minimumReviewReceive) ? true : false;
                }),
                'boolean',

            ],
            'minimum_earn_amount' => ['nullable', 'boolean'],
            'maximum_cancellation_rate' => ['nullable', 'boolean'],
            'minimum_review_receive' => ['nullable', 'boolean'],
            'reward_amount' => [
                Rule::requiredIf(function () use ($rewardType, $id) {
                    return in_array($rewardType, [LOYALTY_POINTS, WALLET]) && empty($id);
                }),
                'numeric',
                function ($attribute, $value, $fail) use ($rewardType) {
                    if ($rewardType === LOYALTY_POINTS && $value <= 0) {
                        $fail('The ' . $attribute . ' must be greater than 0.');
                    }
                    if ($rewardType === WALLET && $value < 0.01) {
                        $fail('The ' . $attribute . ' must be at least 0.01.');
                    }
                },
                // Add 'nullable' as a fallback if the field is not required
                Rule::when(!in_array($rewardType, [LOYALTY_POINTS, WALLET]), 'nullable'),
            ],
            'targeted_ride' => [
                Rule::requiredIf(function () use ($minimumRideComplete) {
                    return $minimumRideComplete;
                }),
                Rule::when($minimumRideComplete == false, 'nullable'),
                'numeric',
                'gt:0',
            ],
            'targeted_ride_point' => [
                Rule::requiredIf(function () use ($minimumRideComplete) {
                    return $minimumRideComplete;
                }),
                Rule::when($minimumRideComplete == false, 'nullable'),
                'numeric',
                'gt:0',
            ],
            'targeted_amount' => [
                Rule::requiredIf(function () use ($minimumEarnAmount, $id) {
                    return $minimumEarnAmount && empty($id);
                }),
                Rule::when($minimumEarnAmount == false, 'nullable'),
                'numeric',
                'gt:0',
            ],
            'targeted_amount_point' => [
                Rule::requiredIf(function () use ($minimumEarnAmount, $id) {
                    return $minimumEarnAmount && empty($id);
                }),
                Rule::when($minimumEarnAmount == false, 'nullable'),
                'numeric',
                'gt:0',
            ],
            'targeted_cancel' => [
                Rule::requiredIf(function () use ($maximumCancellationRate, $id) {
                    return $maximumCancellationRate;
                }),
                Rule::when($maximumCancellationRate == false, 'nullable'),
                'numeric',
                'gt:0',
            ],
            'targeted_cancel_point' => [
                Rule::requiredIf(function () use ($maximumCancellationRate, $id) {
                    return $maximumCancellationRate;
                }),
                Rule::when($maximumCancellationRate == false, 'nullable'),
                'numeric',
                'gt:0',
            ],
            'targeted_review' => [
                Rule::requiredIf(function () use ($minimumReviewReceive, $id) {
                    return $minimumReviewReceive;
                }),
                Rule::when($minimumReviewReceive == false, 'nullable'),
                'numeric',
                'gt:0',
            ],
            'targeted_review_point' => [
                Rule::requiredIf(function () use ($minimumReviewReceive, $id) {
                    return $minimumReviewReceive;
                }),
                Rule::when($minimumReviewReceive == false, 'nullable'),
                'numeric',
                'gt:0',
            ],
            'image' => [
                Rule::requiredIf(empty($id)),
                'image',
                'mimes:png',
                'max:5000']
        ];
    }

    public function messages()
    {
        return [
            'minimum_ride_complete.required' => 'At least one Target is required to create this level.',
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

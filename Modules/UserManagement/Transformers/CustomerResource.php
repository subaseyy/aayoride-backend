<?php

namespace Modules\UserManagement\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\PromotionManagement\Service\AppliedCouponService;
use Modules\PromotionManagement\Transformers\AppliedCouponResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this?->first_name,
            'last_name' => $this?->last_name,
            'level' => CustomerLevelResource::make($this->whenLoaded('level')),
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'identification_number' => $this->identification_number,
            'identification_type' => $this->identification_type,
            'identification_image' => $this->identification_image,
            'other_documents' => $this->other_documents,
            'date_of_birth' => $this->date_of_birth,
            'profile_image' => $this->profile_image,
            'fcm_token' => $this->fcm_token,
            'phone_verified_at' => $this->phone_verified_at,
            'email_verified_at' => $this->email_verified_at,
            'user_type' => $this->user_type,
            'remember_token' => $this->remember_token,
            'is_active' => $this->is_active,
            'loyalty_points' => $this->loyalty_points,
            'last_location' => $this->whenLoaded('lastLocations'),
            'is_profile_verified' => $this->isProfileVerified(),
            'wallet' => UserAccountResource::make($this->whenLoaded('userAccount')),
            'user_rating' => round($this->received_reviews_avg_rating, 1),
            'total_ride_count' => $this->customer_trips_count,
            'completion_percent' => $this->completion_percent,
            'coupon' => AppliedCouponResource::make($this->appliedCoupon)
        ];

    }
}

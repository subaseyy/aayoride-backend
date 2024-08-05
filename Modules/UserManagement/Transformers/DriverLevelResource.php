<?php

namespace Modules\UserManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverLevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sequence' => $this->sequence,
            'name' => $this->name,
            'reward_type' => $this->reward_type,
            'reward_amount' => $this->reward_amount,
            'image' => $this->image,
            'targeted_ride' => $this->targeted_ride,
            'targeted_ride_point' => $this->targeted_ride_point,
            'targeted_amount' => $this->targeted_amount,
            'targeted_amount_point' => $this->targeted_amount_point,
            'targeted_cancel' => $this->targeted_cancel,
            'targeted_cancel_point' => $this->targeted_cancel_point,
            'targeted_review' => $this->targeted_review,
            'targeted_review_point' => $this->targeted_review_point,
            'user_type' => $this->user_type,
            'is_active' => $this->is_active
        ];
    }
}

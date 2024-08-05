<?php

namespace Modules\PromotionManagement\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\UserManagement\Transformers\CustomerLevelResource;
use Modules\UserManagement\Transformers\CustomerResource;

class AppliedCouponResource extends JsonResource
{
    public $preserveKeys = false;

    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "coupon_setup_id" => $this->coupon_setup_id,
        ];
    }
}

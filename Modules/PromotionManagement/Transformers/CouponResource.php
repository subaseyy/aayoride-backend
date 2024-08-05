<?php

namespace Modules\PromotionManagement\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\UserManagement\Transformers\CustomerLevelResource;
use Modules\UserManagement\Transformers\CustomerResource;

class CouponResource extends JsonResource
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
            "name" => $this->name,
            "description" => $this->description,

            "user_id" => $this->whenLoaded(($this->user_id || $this->user_id == 0), function(){
                if ($this->user_id != 0) {
                    return CustomerResource::make($this->customer);
                }else{
                    return 0;
                }
            }) ,
            'customer_id' => $this->user_id,
            "user_level_id" => $this->whenLoaded(($this->user_level_id || is_null($this->user_level_id) ), function(){
                if ($this->user_id != 0) {
                    return CustomerLevelResource::make($this->customer->level);
                }else{
                    return null;
                }
            }),
            'customer_level_id' => $this->user_level_id,
            "min_trip_amount" =>$this->min_trip_amount,
            "max_coupon_amount" => $this->max_coupon_amount,
            "coupon" =>$this->coupon ,
            "amount_type" => $this->amount_type,
            "coupon_type" => $this->coupon_type,
            "coupon_code" => $this->coupon_code,
            "limit" =>$this->limit ,
            "start_date" =>$this->start_date ,
            "end_date" => $this->end_date,
            "rules" => $this->rules,
            "is_active" =>$this->is_active,
            "is_applied" =>$this->is_applied,
            "created_at" => $this->created_at
        ];
    }
}

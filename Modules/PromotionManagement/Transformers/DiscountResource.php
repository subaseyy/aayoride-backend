<?php

namespace Modules\PromotionManagement\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
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
            "title" => $this->title,
            "short_description" => $this->short_description,
            "terms_conditions" => $this->terms_conditions,
            "image" => asset('storage/app/public/promotion/discount/'.$this->image),
            "discount_amount" =>$this->discount_amount ,
            "zone_discount" =>$this->zone_discount ,
            "customer_level_discount" =>$this->customer_level_discount ,
            "customer_discount" =>$this->customer_discount ,
            "module_discount" =>$this->module_discount ,
            "discount_amount_type" => $this->discount_amount_type,
            "max_discount_amount" => $this->max_discount_amount,
            "min_trip_amount" => $this->min_trip_amount,
            "limit" =>$this->limit_per_user ,
            "start_date" =>$this->start_date ,
            "end_date" => $this->end_date,
            "is_active" =>$this->is_active,
            "created_at" => $this->created_at
        ];
    }
}

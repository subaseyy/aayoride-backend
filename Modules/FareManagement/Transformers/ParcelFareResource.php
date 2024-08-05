<?php

namespace Modules\FareManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ZoneManagement\Transformers\ZoneResource;

class ParcelFareResource extends JsonResource
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
            "id"=>$this->id ,
            "zone"=> ZoneResource::make($this->whenLoaded('zone')),
            "base_fare"=>$this->base_fare ,
            "base_fare_per_km"=>$this->base_fare_per_km,
            "cancellation_fee_percent"=>$this->cancellation_fee_percent ,
            "min_cancellation_fee"=>$this->min_cancellation_fee ,
            "created_at"=> $this->created_at,
        ];
    }
}

<?php

namespace Modules\FareManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ParcelManagement\Transformers\ParcelCategoryResource;
use Modules\ParcelManagement\Transformers\ParcelWeightResource;
use Modules\ZoneManagement\Transformers\ZoneResource;

class ParcelFareWeightResource extends JsonResource
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
            "id" => $this->id,
            "parcel_fare" => ParcelFareResource::make($this->whenLoaded('parcelFare')),
            "parcel_weight" => ParcelWeightResource::make($this->whenLoaded('parcel_weight')),
            "parcel_category_id" => ParcelCategoryResource::make($this->whenLoaded('parcel_category')),
            "fare" => $this->fare,
            "zone_id" => ZoneResource::make($this->whenLoaded('zone')),
            "created_at" => $this->created_at,
        ];
    }
}

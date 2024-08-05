<?php

namespace Modules\TripManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\UserManagement\Transformers\CustomerResource;
use Modules\ZoneManagement\Transformers\ZoneResource;

class RecentAddressResource extends JsonResource
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
            'user_id' => CustomerResource::make($this->whenLoaded('customer')),
            'zone_id' => ZoneResource::make($this->whenLoaded('zone')),
            'pickup_coordinates' => $this->pickup_coordinates,
            'destination_coordinates' => $this->destination_coordinates,
            'pickup_address' => $this->pickup_address,
            'destination_address' => $this->destination_address,
        ];
    }
}

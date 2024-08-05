<?php

namespace Modules\FareManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\VehicleManagement\Transformers\VehicleCategoryResource;

class TripFareResource extends JsonResource
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
            'category' => (new VehicleCategoryResource($this->whenLoaded('vehicleCategory'))),
            'id' => $this->id,
            'base_fare' => $this->base_fare,
            'base_fare_per_km' => $this->base_fare_per_km,
            'waiting_fee_per_min' => $this->waiting_fee_per_min,
            'min_cancellation_fee' => $this->min_cancellation_fee,
            'idle_fee_per_min' => $this->idle_fee_per_min,
            'trip_delay_fee_per_min' => $this->trip_delay_fee_per_min,
            'penalty_fee_for_cancel' => $this->penalty_fee_for_cancel,
            'fee_add_to_next' => $this->fee_add_to_next,
            'created_at' => $this->created_at,
        ];
    }
}

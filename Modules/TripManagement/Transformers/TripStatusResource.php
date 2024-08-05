<?php

namespace Modules\TripManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\UserManagement\Transformers\CustomerResource;

class TripStatusResource extends JsonResource
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
            'trip_request' => TripRequestResource::make($this->whenLoaded('trip_request')),
            'customer_id' => CustomerResource::make($this->whenLoaded('customer')),
            'pending' => $this->pending,
            'accepted' => $this->accepted,
            'confirmed' => $this->confirmed,
            'out_for_pickup' => $this->out_for_pickup,
            'picked_up' => $this->picked_up,
            'ongoing' => $this->ongoing,
            'completed' => $this->completed,
            'rejected' => $this->rejected,
            'cancelled' => $this->cancelled,
            'failed' => $this->failed,
            'note' => $this->note,
        ];
    }
}

<?php

namespace Modules\TripManagement\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\UserManagement\Transformers\CustomerResource;
use Modules\UserManagement\Transformers\DriverResource;
use Modules\UserManagement\Transformers\LastLocationResource;

class FareBiddingResource extends JsonResource
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
            'trip_requests_id' => $this->trip_request_id,
            'driver' => DriverResource::make($this->whenLoaded('driver')),
            'trip_request' => TripRequestResource::make($this->whenLoaded('trip_request')),
            'driver_last_location' => LastLocationResource::make($this->whenLoaded('driver_last_location')),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'bid_fare' => $this->bid_fare,
            'customer_avg_rating' => $this->customer_received_reviews_avg_rating,
            'driver_avg_rating' => $this->driverReceivedReviews_avg_rating,
            'is_ignored' => $this->is_ignored,
        ];
    }
}

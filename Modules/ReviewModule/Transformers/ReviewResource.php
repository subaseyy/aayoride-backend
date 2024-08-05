<?php

namespace Modules\ReviewModule\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\TripManagement\Transformers\TripRequestResource;
use Modules\UserManagement\Transformers\CustomerResource;

class ReviewResource extends JsonResource
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
            'trip_request_id' => $this->trip_request_id,
            'trip' => TripRequestResource::make($this->whenLoaded('trip')),
            'trip_ref_id' =>$this->whenLoaded(relationship: 'trip', value: $this->trip->ref_id),
            'given_user' => CustomerResource::make($this->whenLoaded('givenUser')),
            'trip_type' => $this->trip_type,
            'rating' => $this->rating,
            'feedback' => $this->feedback,
            'is_saved' => (boolean) $this->is_saved,
            'created_at' => $this->created_at,
        ];
    }
}

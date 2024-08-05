<?php

namespace Modules\ZoneManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\FareManagement\Transformers\TripFareResource;

class ZoneResource extends JsonResource
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
            'name' => $this->name,
            'coordinates' => $this->coordinates,
            'is_active' => $this->is_active,
            'tripFares' => TripFareResource::collection($this->whenLoaded('tripFares')),
            'created_at' => $this->created_at
        ];
    }
}

<?php

namespace Modules\VehicleManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleModelResource extends JsonResource
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
            'brand' => (new VehicleBrandResource($this->whenLoaded('brand'))),
            'seat_capacity' => $this->seat_capacity,
            'maximum_weight' => $this->maximum_weight,
            'hatch_bag_capacity' => $this->hatch_bag_capacity,
            'engine' => $this->engine,
            'description' => $this->description,
            'image' => $this->image,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at
        ];
    }
}

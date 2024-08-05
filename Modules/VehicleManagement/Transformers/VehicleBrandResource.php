<?php

namespace Modules\VehicleManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleBrandResource extends JsonResource
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
            'description' => $this->description,
            'image' => $this->image,
            'is_active' => $this->is_active,
            'vehicles' => VehicleResource::collection($this->whenLoaded('vehicles')),
            'vehicle_models' => VehicleModelResource::collection($this->whenLoaded('vehicleModels')),
            'created_at' => $this->created_at
        ];
    }
}

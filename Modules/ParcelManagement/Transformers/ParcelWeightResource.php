<?php

namespace Modules\ParcelManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ParcelWeightResource extends JsonResource
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
            'min_weight' => $this->min_weight,
            'max_weight' => $this->max_weight,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}

<?php

namespace Modules\ParcelManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'contact_number' => $this->contact_number,
            'name' => $this->name,
            'address' => $this->address,
            'user_type' => $this->user_type,
        ];
    }
}

<?php

namespace Modules\UserManagement\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            "id" => $this->id,
            "user_id" => $this->user_id,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "city" => $this->city,
            "street" => $this->street,
            "house" => $this->house,
            "zip_code" => $this->zip_code,
            "country" => $this->country,
            "contact_person_name" => $this->contact_person_name,
            "contact_person_phone" => $this->contact_person_phone,
            "address" => $this->address,
            "address_label" => $this->address_label,
            "created_at" => $this->created_at,
        ];
    }
}

<?php

namespace Modules\ParcelManagement\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformationResource extends JsonResource
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
            'parcel_category_id' => $this->parcel_category_id,
            'payer' => $this->payer,
            'weight' => (double)$this->weight
        ];
    }
}

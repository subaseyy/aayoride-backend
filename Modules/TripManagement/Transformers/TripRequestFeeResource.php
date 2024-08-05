<?php

namespace Modules\TripManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TripRequestFeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'trip_request_id' => $this->trip_request_id,
            'cancellation_fee' => $this->cancellation_fee,
            'cancelled_by' => $this->cancelled_by,
            'waiting_fee' => $this->waiting_fee,
            'waited_by' => $this->waited_by,
            'idle_fee' => $this->idle_fee,
            'delay_fee' => $this->delay_fee,
            'delayed_by' => $this->delayed_by,
            'vat_tax' => $this->vat_tax,
            'tips' => $this->tips,
            'admin_commission' => $this->admin_commission,
            'created_at' => $this->created_at,
        ];
    }
}

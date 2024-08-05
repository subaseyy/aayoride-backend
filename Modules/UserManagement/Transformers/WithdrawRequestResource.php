<?php

namespace Modules\UserManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawRequestResource extends JsonResource
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
            'user' => DriverResource::make($this->user),
            'method' => WithdrawMethodResource::make($this->method),
            'method_fields' => $this->method_fields,
            'amount' => $this->amount,
            'driver_note' =>  $this->driver_note,
            'approval_note' =>  $this->approval_note,
            'denied_note' =>  $this->denied_note,
            'status' =>  $this->status,
            'created_at' =>  $this->created_at,
        ];
    }
}

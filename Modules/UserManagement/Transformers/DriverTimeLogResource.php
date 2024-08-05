<?php

namespace Modules\UserManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverTimeLogResource extends JsonResource
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
            'online_time' => $this->online_time,
            'idle_time' => $this->idle_time,
            'on_driving_time' => $this->on_driving_time,
            'date' => $this->date,
            'driver' => DriverResource::make($this->whenLoaded('driver')),
        ];
    }
}

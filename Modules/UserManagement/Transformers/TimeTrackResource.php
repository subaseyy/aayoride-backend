<?php

namespace Modules\UserManagement\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeTrackResource extends JsonResource
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
            'id' => $this->id,
            'date' => $this->date,
            'total_online' => $this->total_online,
            'total_offline' => $this->total_offline,
            'total_idle' => $this->total_idle,
            'total_driving' => $this->total_driving,
        ];
    }
}

<?php

namespace Modules\UserManagement\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverLeaderBoardResourse extends JsonResource
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
            'driver_id' => $this->driver_id,
            'total_records' => $this->total_records,
            'income' => $this->income,
            'driver' => $this->whenLoaded('driver'),
        ];

        // return [
        //     'driver_id' => $this->driver_id,
        //     'total_records' => $this->accepted_rides, // Only accepted rides
        //     'total_income' => $this->income, // Total income including commission
        //     'commission' => $this->commission, // Commission amount
        //     'driver' => $this->whenLoaded('driver'),
        // ];
    }
}

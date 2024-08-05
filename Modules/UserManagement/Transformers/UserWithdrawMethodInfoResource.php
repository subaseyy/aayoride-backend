<?php

namespace Modules\UserManagement\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ParcelManagement\Transformers\UserResource;

class UserWithdrawMethodInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [];
        foreach ($this->method_info as $key => $value) {
            $data[]=[
                'key'=>$key,
                'value'=>$value,
            ];
        }
        return [
            'id' => $this->id,
            'method_name' => $this->method_name,
            'user' => DriverResource::make($this->user),
            'withdraw_method' => WithdrawMethodResource::make($this->withdrawMethod),
            'method_info' => $data,
            'is_active' => (bool) $this->is_active,
        ];
    }
}

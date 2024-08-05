<?php

namespace Modules\UserManagement\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyPointsHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->model == 'user_level') {
            $model = 'level_up';
        } elseif($this->model == 'userAccount' && $this->type == 'debit') {
            $model = 'point_converted';
        } else {
            $model = 'point_converted';
        }
        return [
            'user_id' => $this->user_id,
            'model' => $model,
            'model_id' => $this->model_id,
            'points' => $this->points,
            'type' => $this->type,
            'created_at' => $this->created_at,
        ];
    }
}

<?php

namespace Modules\UserManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAccountResource extends JsonResource
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
            'payable_balance' => (double) $this->payable_balance,
            'receivable_balance' => (double) $this->receivable_balance,
            'received_balance' => (double) $this->received_balance,
            'pending_balance' => (double) $this->pending_balance,
            'wallet_balance' => (double) $this->wallet_balance,
            'total_withdrawn' => (double) $this->total_withdrawn,
        ];
    }
}

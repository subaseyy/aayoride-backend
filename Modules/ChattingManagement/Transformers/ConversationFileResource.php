<?php

namespace Modules\ChattingManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ConversationFileResource extends JsonResource
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
            'file_name' => $this->file_name,
            'file_type' => $this->file_type
        ];
    }
}

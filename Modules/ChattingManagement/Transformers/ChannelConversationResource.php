<?php

namespace Modules\ChattingManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\UserManagement\Transformers\CustomerResource;

class ChannelConversationResource extends JsonResource
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
            'message' => $this->message,
            'trip_id' => $this->convable->ref_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'conversation_files' => ConversationFileResource::collection($this->whenLoaded('conversation_files')),
            'channel' => ChannelListResource::make($this->whenLoaded('channel')),
            'user' => CustomerResource::make($this->whenLoaded('user')),

        ];
    }
}

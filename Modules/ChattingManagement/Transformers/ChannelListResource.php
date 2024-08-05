<?php

namespace Modules\ChattingManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ChannelListResource extends JsonResource
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
            'trip_id' => $this->channelable_id,
            'updated_at' => $this->updated_at,
            'channel_users' => ChannelUserResource::collection($this->whenLoaded('channel_users')),
            'last_channel_conversations' => $this->whenLoaded('last_channel_conversations'),
            'unread_customer_channel_conversations' => $this->whenLoaded('channel_conversations', value: function () {
                    foreach ($this->channel_users as $channel_user) {
                        if ($channel_user->user->user_type == CUSTOMER){
                            $userId = $channel_user->user->id;
                            return $this->channel_conversations->where('user_id',$userId)->where('is_read', 0)->count();
                        }
                    }
                }) ?? 0,
            'unread_driver_channel_conversations' => $this->whenLoaded('channel_conversations', function () {
                    foreach ($this->channel_users as $channel_user) {
                        if ($channel_user->user->user_type == DRIVER){
                            $userId = $channel_user->user->id;
                            return $this->channel_conversations->where('user_id',$userId)->where('is_read', 0)->count();
                        }
                    }
                }) ?? 0,
        ];
    }
}

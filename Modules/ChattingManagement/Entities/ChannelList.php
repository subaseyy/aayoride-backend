<?php

namespace Modules\ChattingManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\UserManagement\Entities\UserLevelHistory;

class ChannelList extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'created_at',
        'channelable_id'
    ];

    protected static function newFactory()
    {
        return \Modules\ChatModule\Database\factories\ChannelListFactory::new();
    }

    public function channelable(): MorphTo
    {
        return $this->morphTo();
    }

    public function channel_users()
    {
        return $this->hasMany(ChannelUser::class, 'channel_id');
    }
    public function channel_conversations()
    {
        return $this->hasMany(ChannelConversation::class, 'channel_id');
    }

    public function last_channel_conversations(){
        return $this->hasOne(ChannelConversation::class, 'channel_id')->latestOfMany();
    }

}

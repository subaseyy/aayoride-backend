<?php

namespace Modules\ChattingManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\UserManagement\Entities\User;

class ChannelConversation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'channel_id',
        'user_id',
        'message',
        'is_read',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_read'=>'boolean',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected static function newFactory()
    {
        return \Modules\ChatModule\Database\factories\ChannelConversationFactory::new();
    }

    public function conversation_files(){
        return $this->hasMany(ConversationFile::class, 'conversation_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function channel(){
        return $this->belongsTo(ChannelList::class);
    }
    public function convable(): MorphTo
    {
        return $this->morphTo();
    }

}

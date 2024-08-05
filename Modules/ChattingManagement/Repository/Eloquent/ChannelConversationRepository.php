<?php

namespace Modules\ChattingManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\ChattingManagement\Entities\ChannelConversation;
use Modules\ChattingManagement\Repository\ChannelConversationRepositoryInterface;

class ChannelConversationRepository extends BaseRepository implements ChannelConversationRepositoryInterface
{
    public function __construct(ChannelConversation $model)
    {
        parent::__construct($model);
    }
}

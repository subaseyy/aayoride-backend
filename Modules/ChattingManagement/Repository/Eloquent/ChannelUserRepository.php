<?php

namespace Modules\ChattingManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\ChattingManagement\Entities\ChannelUser;
use Modules\ChattingManagement\Repository\ChannelUserRepositoryInterface;

class ChannelUserRepository extends BaseRepository implements ChannelUserRepositoryInterface
{
    public function __construct(ChannelUser $model)
    {
        parent::__construct($model);
    }
}

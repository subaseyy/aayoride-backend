<?php

namespace Modules\ChattingManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\ChattingManagement\Entities\ChannelList;
use Modules\ChattingManagement\Repository\ChannelListRepositoryInterface;

class ChannelListRepository extends BaseRepository implements ChannelListRepositoryInterface
{
    public function __construct(ChannelList $model)
    {
        parent::__construct($model);
    }
}

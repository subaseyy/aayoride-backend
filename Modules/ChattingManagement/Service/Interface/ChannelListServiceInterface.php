<?php

namespace Modules\ChattingManagement\Service\Interface;

use App\Service\BaseServiceInterface;
use Illuminate\Database\Eloquent\Model;

interface ChannelListServiceInterface extends BaseServiceInterface
{
    public function createChannelWithChannelUser(array $data): ?Model;
}

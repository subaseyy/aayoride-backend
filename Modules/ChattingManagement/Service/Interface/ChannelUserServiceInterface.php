<?php

namespace Modules\ChattingManagement\Service\Interface;

use App\Service\BaseServiceInterface;

interface ChannelUserServiceInterface extends BaseServiceInterface
{
public function sendMessageChannelUserupdate($data);
}

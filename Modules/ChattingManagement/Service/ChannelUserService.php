<?php

namespace Modules\ChattingManagement\Service;

use App\Repository\EloquentRepositoryInterface;
use App\Service\BaseService;
use Modules\ChattingManagement\Repository\ChannelUserRepositoryInterface;
use Modules\ChattingManagement\Service\Interface\ChannelUserServiceInterface;

class ChannelUserService extends BaseService implements Interface\ChannelUserServiceInterface
{
    protected $channelUserRepository;
    public function __construct(ChannelUserRepositoryInterface $channelUserRepository)
    {
        parent::__construct($channelUserRepository);
        $this->channelUserRepository = $channelUserRepository;
    }

   public function sendMessageChannelUserupdate($data)  {
    $oparetor = $data['is_read'] ? '=' : '!=';
    $user = $this->channelUserRepository->findOneBy([['channel_id','=',$data['channel_id']],['user_id', $oparetor,$data['user_id']]]);
    $user->is_read = $data['is_read'];
    $user->save();
    return $user;
   }

}

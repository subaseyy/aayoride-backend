<?php

namespace Modules\ChattingManagement\Service;

use App\Repository\EloquentRepositoryInterface;
use App\Service\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\ChattingManagement\Repository\ChannelListRepositoryInterface;
use Modules\ChattingManagement\Service\Interface\ChannelListServiceInterface;
use Modules\TripManagement\Repository\Eloquent\TripRequestRepository;

class ChannelListService extends BaseService implements Interface\ChannelListServiceInterface
{
    protected $channelListRepository;
    protected $tripRequestRepository;
    public function __construct(ChannelListRepositoryInterface $channelListRepository, TripRequestRepository $tripRequestRepository)
    {
        parent::__construct($channelListRepository);
        $this->channelListRepository = $channelListRepository;
        $this->tripRequestRepository = $tripRequestRepository;
    }



    public function createChannelWithChannelUser(array $data): Model
    {
        $trip = $this->tripRequestRepository->findOne($data['trip_id']);
        $value = [
            ['user_id' => $trip?->customer_id],
            ['user_id' => $trip?->driver_id]
        ];
        $channel = $trip?->channel()->create(['created_at'=>now()]);
        $channel->channel_users()->createMany($value);
        return $channel;

//        $value = [
//            ['user_id' => Auth::user()->id],
//            ['user_id' => $data['to']]
//        ];
//        $channel = $this->channelListRepository->create(['created_at'=>now()]);
//        $channel->channel_users()->createMany($value);
//        return $channel;
    }
}

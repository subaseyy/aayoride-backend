<?php

namespace Modules\ChattingManagement\Service;

use App\Service\BaseService;
use Illuminate\Database\Eloquent\Model;
use Modules\ChattingManagement\Repository\ChannelConversationRepositoryInterface;
use Modules\TripManagement\Repository\TripRequestRepositoryInterface;

class ChannelConversationService extends BaseService implements Interface\ChannelConversationServiceInterface
{
    protected $channelConversationRepository;
    protected $tripRequestRepository;
    public function __construct(ChannelConversationRepositoryInterface $channelConversationRepository, TripRequestRepositoryInterface $tripRequestRepository)
    {
        parent::__construct($channelConversationRepository);
        $this->channelConversationRepository = $channelConversationRepository;
        $this->tripRequestRepository = $tripRequestRepository;
    }

    public function create(array $data): ?Model
    {
        $trip = $this->tripRequestRepository->findOne($data['trip_id']);
        $conversation = $trip?->conversations()->create($data);
        if (array_key_exists('files', $data)) {
            foreach ($data['files'] as $file) {
                $extension = $file->getClientOriginalExtension();
                $conversation?->conversation_files()->create([
                    'file_name' => fileUploader('conversation/', $extension, $file),
                    'file_type' => $extension,
                ]);
            }
        }
        return $conversation;
    }
}

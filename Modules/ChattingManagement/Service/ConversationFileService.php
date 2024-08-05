<?php

namespace Modules\ChattingManagement\Service;

use App\Service\BaseService;
use Modules\ChattingManagement\Repository\ConversationFileRepositoryInterface;
use Modules\ChattingManagement\Service\Interface\ConversationFileServiceInterface;

class ConversationFileService extends BaseService implements Interface\ConversationFileServiceInterface
{
    public function __construct(ConversationFileRepositoryInterface $baseRepository)
    {
        parent::__construct($baseRepository);
    }
}

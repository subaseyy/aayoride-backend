<?php

namespace Modules\ChattingManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\ChattingManagement\Entities\ConversationFile;
use Modules\ChattingManagement\Repository\ConversationFileRepositoryInterface;

class ConversationFileRepository extends BaseRepository implements ConversationFileRepositoryInterface
{
    public function __construct(ConversationFile $model)
    {
        parent::__construct($model);
    }
}

<?php

namespace Modules\AdminModule\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\AdminModule\Entities\ActivityLog;
use Modules\AdminModule\Repository\ActivityLogRepositoryInterface;

class ActivityLogRepository extends BaseRepository implements ActivityLogRepositoryInterface
{
    public function __construct(ActivityLog $model)
    {
        parent::__construct($model);
    }
}

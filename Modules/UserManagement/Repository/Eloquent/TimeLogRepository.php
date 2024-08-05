<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\TimeLog;
use Modules\UserManagement\Repository\TimeLogRepositoryInterface;

class TimeLogRepository extends BaseRepository implements TimeLogRepositoryInterface
{
    public function __construct(TimeLog $model)
    {
        parent::__construct($model);
    }
}

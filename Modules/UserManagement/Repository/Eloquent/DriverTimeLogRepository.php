<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\DriverTimeLog;
use Modules\UserManagement\Repository\DriverTimeLogRepositoryInterface;

class DriverTimeLogRepository extends BaseRepository implements DriverTimeLogRepositoryInterface
{
    public function __construct(DriverTimeLog $model)
    {
        parent::__construct($model);
    }
}

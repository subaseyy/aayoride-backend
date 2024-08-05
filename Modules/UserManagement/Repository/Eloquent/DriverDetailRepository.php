<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\DriverDetail;
use Modules\UserManagement\Repository\DriverDetailRepositoryInterface;

class DriverDetailRepository extends BaseRepository implements DriverDetailRepositoryInterface
{
    public function __construct(DriverDetail $model)
    {
        parent::__construct($model);
    }
}

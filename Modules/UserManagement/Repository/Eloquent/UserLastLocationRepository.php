<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\UserLastLocation;
use Modules\UserManagement\Repository\UserLastLocationRepositoryInterface;

class UserLastLocationRepository extends BaseRepository implements UserLastLocationRepositoryInterface
{
    public function __construct(UserLastLocation $model)
    {
        parent::__construct($model);
    }
}

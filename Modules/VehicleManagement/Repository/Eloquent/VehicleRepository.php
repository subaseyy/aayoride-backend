<?php

namespace Modules\VehicleManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\VehicleManagement\Entities\Vehicle;
use Modules\VehicleManagement\Repository\VehicleRepositoryInterface;

class VehicleRepository extends BaseRepository implements VehicleRepositoryInterface
{
    public function __construct(Vehicle $model)
    {
        parent::__construct($model);
    }
}

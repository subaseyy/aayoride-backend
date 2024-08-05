<?php

namespace Modules\VehicleManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\VehicleManagement\Entities\VehicleModel;
use Modules\VehicleManagement\Repository\VehicleModelRepositoryInterface;

class VehicleModelRepository extends BaseRepository implements VehicleModelRepositoryInterface
{
    public function __construct(VehicleModel $model)
    {
        parent::__construct($model);
    }
}

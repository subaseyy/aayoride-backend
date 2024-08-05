<?php

namespace Modules\VehicleManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\VehicleManagement\Entities\VehicleBrand;
use Modules\VehicleManagement\Repository\VehicleBrandRepositoryInterface;

class VehicleBrandRepository extends BaseRepository implements VehicleBrandRepositoryInterface
{
    public function __construct(VehicleBrand $model)
    {
        parent::__construct($model);
    }
}

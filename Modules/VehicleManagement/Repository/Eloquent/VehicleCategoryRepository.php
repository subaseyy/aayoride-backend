<?php

namespace Modules\VehicleManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\VehicleManagement\Entities\VehicleCategory;
use Modules\VehicleManagement\Repository\VehicleCategoryRepositoryInterface;

class VehicleCategoryRepository extends BaseRepository implements VehicleCategoryRepositoryInterface
{
    public function __construct(VehicleCategory $model)
    {
        parent::__construct($model);
    }
}

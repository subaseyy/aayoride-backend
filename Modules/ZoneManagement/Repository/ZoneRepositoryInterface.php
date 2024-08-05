<?php

namespace Modules\ZoneManagement\Repository;

use App\Repository\EloquentRepositoryInterface;

interface ZoneRepositoryInterface extends EloquentRepositoryInterface
{
    public function getByPoints($point);
}

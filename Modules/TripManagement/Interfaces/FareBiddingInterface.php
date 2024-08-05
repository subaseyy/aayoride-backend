<?php

namespace Modules\TripManagement\Interfaces;


use App\Repositories\Interfaces\BaseRepositoryInterface;

interface FareBiddingInterface extends BaseRepositoryInterface
{
    function destroyData($attributes): mixed;
}

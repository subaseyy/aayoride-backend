<?php

namespace Modules\TripManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\TripManagement\Entities\TripRoute;
use Modules\TripManagement\Repository\TripRouteRepositoryInterface;

class TripRouteRepository extends BaseRepository implements TripRouteRepositoryInterface
{
    public function __construct(TripRoute $model)
    {
        parent::__construct($model);
    }
}

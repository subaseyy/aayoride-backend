<?php

namespace Modules\TripManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\TripManagement\Entities\TripRequestCoordinate;
use Modules\TripManagement\Repository\TripRequestCoordinateRepositoryInterface;

class TripRequestCoordinateRepository extends BaseRepository implements TripRequestCoordinateRepositoryInterface
{
    public function __construct(TripRequestCoordinate $model)
    {
        parent::__construct($model);
    }
}

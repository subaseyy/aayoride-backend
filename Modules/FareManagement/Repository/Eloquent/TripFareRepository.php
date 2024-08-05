<?php

namespace Modules\FareManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\FareManagement\Entities\TripFare;
use Modules\FareManagement\Repository\TripFareRepositoryInterface;

class TripFareRepository extends BaseRepository implements TripFareRepositoryInterface
{

    public function __construct(TripFare $model)
    {
        parent::__construct($model);
    }
}

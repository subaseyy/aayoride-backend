<?php

namespace Modules\FareManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\FareManagement\Entities\ZoneWiseDefaultTripFare;
use Modules\FareManagement\Repository\ZoneWiseDefaultTripFareRepositoryInterface;

class ZoneWiseDefaultTripFareRepository extends BaseRepository implements ZoneWiseDefaultTripFareRepositoryInterface
{
    public function __construct(ZoneWiseDefaultTripFare $model)
    {
        parent::__construct($model);
    }
}

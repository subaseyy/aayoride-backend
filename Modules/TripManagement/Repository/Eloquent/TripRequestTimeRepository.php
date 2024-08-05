<?php

namespace Modules\TripManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\TripManagement\Entities\TripRequestTime;
use Modules\TripManagement\Repository\TripRequestTimeRepositoryInterface;

class TripRequestTimeRepository extends BaseRepository implements TripRequestTimeRepositoryInterface
{
    public function __construct(TripRequestTime $model)
    {
        parent::__construct($model);
    }
}

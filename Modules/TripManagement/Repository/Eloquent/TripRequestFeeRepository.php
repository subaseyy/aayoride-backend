<?php

namespace Modules\TripManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\TripManagement\Entities\TripRequestFee;
use Modules\TripManagement\Repository\TripRequestFeeRepositoryInterface;

class TripRequestFeeRepository extends BaseRepository implements TripRequestFeeRepositoryInterface
{
    public function __construct(TripRequestFee $model)
    {
        parent::__construct($model);
    }
}

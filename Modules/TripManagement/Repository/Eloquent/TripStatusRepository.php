<?php

namespace Modules\TripManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\TripManagement\Entities\TripStatus;
use Modules\TripManagement\Repository\TripStatusRepositoryInterface;

class TripStatusRepository extends BaseRepository implements TripStatusRepositoryInterface
{
    public function __construct(TripStatus $model)
    {
        parent::__construct($model);
    }
}

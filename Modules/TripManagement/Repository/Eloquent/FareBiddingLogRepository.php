<?php

namespace Modules\TripManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\TripManagement\Entities\FareBiddingLog;
use Modules\TripManagement\Repository\FareBiddingLogRepositoryInterface;

class FareBiddingLogRepository extends BaseRepository implements FareBiddingLogRepositoryInterface
{
    public function __construct(FareBiddingLog $model)
    {
        parent::__construct($model);
    }

}

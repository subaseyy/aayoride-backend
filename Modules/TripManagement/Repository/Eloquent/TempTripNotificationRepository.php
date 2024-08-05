<?php

namespace Modules\TripManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\TripManagement\Entities\TempTripNotification;
use Modules\TripManagement\Repository\TempTripNotificationRepositoryInterface;

class TempTripNotificationRepository extends BaseRepository implements TempTripNotificationRepositoryInterface
{
    public function __construct(TempTripNotification $model)
    {
        parent::__construct($model);
    }
}

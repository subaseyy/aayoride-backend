<?php

namespace Modules\TripManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\TripManagement\Entities\RecentAddress;
use Modules\TripManagement\Repository\RecentAddressRepositoryInterface;

class RecentAddressRepository extends BaseRepository implements RecentAddressRepositoryInterface
{
    public function __construct(RecentAddress $model)
    {
        parent::__construct($model);
    }
}

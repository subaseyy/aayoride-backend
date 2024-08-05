<?php

namespace Modules\TripManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\TripManagement\Entities\RejectedDriverRequest;
use Modules\TripManagement\Repository\RejectedDriverRequestRepositoryInterface;

class RejectedDriverRequestRepository extends BaseRepository implements RejectedDriverRequestRepositoryInterface
{
    public function __construct(RejectedDriverRequest $model)
    {
        parent::__construct($model);
    }
}

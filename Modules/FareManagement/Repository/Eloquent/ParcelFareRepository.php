<?php

namespace Modules\FareManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\FareManagement\Entities\ParcelFare;
use Modules\FareManagement\Repository\ParcelFareRepositoryInterface;

class ParcelFareRepository extends BaseRepository implements ParcelFareRepositoryInterface
{

    public function __construct(ParcelFare $model)
    {
        parent::__construct($model);
    }
}

<?php

namespace Modules\FareManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\FareManagement\Entities\ParcelFareWeight;
use Modules\FareManagement\Repository\ParcelFareWeightRepositoryInterface;

class ParcelFareWeightRepository extends BaseRepository implements ParcelFareWeightRepositoryInterface
{

    public function __construct(ParcelFareWeight $model)
    {
        parent::__construct($model);
    }
}

<?php

namespace Modules\FareManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface ParcelFareInterface extends BaseRepositoryInterface
{
    /**
     * From ParcelFareWeight table get parcel fares by vehicle cat id, weight id and zone id
     * @param array $attributes
     * @param array $relations
     * @return mixed
     */
    public function categorizedFares(array $attributes,array $relations = []):mixed;
    public function getZoneFare(array $attributes):mixed;
}

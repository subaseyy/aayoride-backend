<?php

namespace Modules\TripManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface TripRequestInterfaces extends BaseRepositoryInterface
{
    /**
     * @param array $attributes
     * @return mixed
     */
    public function updateRelationalTable(array $attributes):mixed;

    /**
     * @param array $attributes
     * @return mixed
     */
    public function getPendingRides(array $attributes):mixed;
    public function leaderBoard(array $attributes);
    public function getStat(array $attributes);
    public function getIncompleteRide(array $attributes);

    public function overviewStat (array $attributes);

    public function trashed(array $attributes);
    public function pendingParcelList(array $attributes, string $type);

    public function unpaidParcelRequest(array $attributes);

}

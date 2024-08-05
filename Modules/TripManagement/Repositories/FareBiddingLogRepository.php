<?php

namespace Modules\TripManagement\Repositories;


use Modules\TripManagement\Entities\FareBiddingLog;
use Modules\TripManagement\Interfaces\FareBiddingLogInterface;

class FareBiddingLogRepository implements FareBiddingLogInterface
{

    public function __construct(private FareBiddingLog $log)
    {
    }


    /**
     * @param array $attributes
     * @return mixed
     */
    public function store($attributes): mixed
    {
        return $this->log::query()
            ->create($attributes);
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function storeAll($attributes): mixed
    {
        return $this->log::query()
            ->insert($attributes);
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function destroyData($attributes): mixed
    {
        return $this->log
            ->query()
            ->whereIn($attributes['column'], $attributes['ids'])
            ->delete();
    }


}

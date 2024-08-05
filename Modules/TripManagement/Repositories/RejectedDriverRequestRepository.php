<?php

namespace Modules\TripManagement\Repositories;

use Modules\TripManagement\Entities\RejectedDriverRequest;
use Modules\TripManagement\Interfaces\RejectedDriverRequestInterface;

class RejectedDriverRequestRepository implements RejectedDriverRequestInterface
{
    public function __construct(private RejectedDriverRequest $request)
    {
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    function store(array $attributes): mixed
    {
        $rejectedRequest = $this->request;
        $rejectedRequest->trip_request_id = $attributes['trip_request_id'];
        $rejectedRequest->user_id = $attributes['user_id'];
        $rejectedRequest->save();

        return $rejectedRequest;
    }


    /**
     * @param $attributes
     * @return mixed
     */
    public function destroyData($attributes): mixed
    {
        return $this->request
            ->query()
            ->where($attributes['column'], $attributes['value'])
            ->delete();
    }
}

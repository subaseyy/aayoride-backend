<?php

namespace Modules\TripManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\TripManagement\Entities\TempTripNotification;
use Modules\TripManagement\Interfaces\TempTripNotificationInterface;

class TempTripNotificationRepository implements TempTripNotificationInterface
{
    public function __construct(
        private TempTripNotification $notification
    )
    {
    }

    public function get(array $attributes)
    {
        return $this->notification->query()
            ->when(array_key_exists('trip_request_id', $attributes), function ($query) use ($attributes){
                $query->where('trip_request_id', $attributes['trip_request_id']);
            })
            ->when(array_key_exists('relations', $attributes), fn($query) => $query->with($attributes['relations']))
            ->when(array_key_exists('whereNotInColumn', $attributes), function ($query) use($attributes){
                $query->whereNotIn($attributes['whereNotInColumn'], $attributes['whereNotInValue']);
            })
            ->get();
    }

    public function getBy(array $attributes)
    {
        return $this->notification->query()
            ->where('trip_request_id', $attributes['trip_request_id'])
            ->where('user_id', $attributes['user_id'])
            ->first();
    }

    public function store($attributes)
    {
        return $this->notification->query()
            ->insert($attributes['data']);
    }

    public function delete($trip_request_id)
    {
        return $this->notification->query()
            ->where('trip_request_id', $trip_request_id)
            ->delete();
    }

    public function ignoreNotification(array $attributes)
    {
        return $this->notification->query()
            ->where('trip_request_id', $attributes['trip_request_id'])
            ->where('user_id', $attributes['user_id'])
            ->first()
            ->delete();
    }


}

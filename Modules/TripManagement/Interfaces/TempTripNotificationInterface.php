<?php

namespace Modules\TripManagement\Interfaces;

interface TempTripNotificationInterface
{
    public function get(array $attributes);
    public function store($attributes);
    public function delete($trip_request_id);

    public function ignoreNotification(array $attributes);
    public function getBy(array $attributes);

}

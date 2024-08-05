<?php

namespace Modules\TripManagement\Interfaces;

interface RejectedDriverRequestInterface
{
    function store(array $attributes): mixed;
    function destroyData(array $attributes): mixed;
}

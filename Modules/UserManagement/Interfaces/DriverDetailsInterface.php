<?php

namespace Modules\UserManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface DriverDetailsInterface extends BaseRepositoryInterface
{
     /**
     * @param $driver_id
     * @param $date
     * @param $online
     * @param $offline
     */
    public function setTimeLog($driver_id , $date, $online = null, $offline = null, $accepted=null, $completed=null, $start_driving=null, $trip = null, $activeLog = false);

}

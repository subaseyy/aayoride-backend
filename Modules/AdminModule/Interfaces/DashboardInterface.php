<?php

namespace Modules\AdminModule\Interfaces;



use App\Repositories\Interfaces\BaseRepositoryInterface;

interface DashboardInterface
{
    public function getZoneWithcenter();

    public function leaderBoard($attributes);

    public function adminEarning($attributes);

    public function zoneStatistics($attributes);


}

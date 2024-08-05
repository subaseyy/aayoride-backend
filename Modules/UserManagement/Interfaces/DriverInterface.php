<?php

namespace Modules\UserManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface DriverInterface extends BaseRepositoryInterface
{

    public function updateFcm($fcm): mixed;
    public function getCount($countColumn, $filterColumn = null, $filterValue = null, array $attributes = null);

    public function getStatisticsData(array $attributes);

    public function getDriverWithoutVehicle(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes= [], array $relations = []);

    public function trashed(array $attributes);

    public function restore(string $id);

    public function permanentDelete(string $id);
}

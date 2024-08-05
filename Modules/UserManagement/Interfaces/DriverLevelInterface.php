<?php

namespace Modules\UserManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface DriverLevelInterface extends BaseRepositoryInterface
{
    public function getLevelizedTrips(array $attributes, $export = false): mixed;

    public function getFirstLevel(): mixed;

    public function trashed(array $attributes);

    public function restore(string $id);

    public function permanentDelete(string $id);

}

<?php

namespace Modules\ZoneManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface ZoneInterface extends BaseRepositoryInterface
{
    /**
     * Get Zone Contains by passing points
     * @param $point
     * @return mixed
     */
    public function getByPoints($point):mixed;

    public function trashed(array $attributes);

    public function restore(string $id);

    public function permanentDelete(string $id);

    public function storeWithException($attributes);

}

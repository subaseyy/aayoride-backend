<?php

namespace Modules\VehicleManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface VehicleModelInterface extends BaseRepositoryInterface
{
    /**
     * @param array $attributes
     * @return mixed
     */
    public function getByComparison(array $attributes):mixed;

    public function trashed(array $attributes);

    public function restore(string $id);
    public function permanentDelete(string $id);
}

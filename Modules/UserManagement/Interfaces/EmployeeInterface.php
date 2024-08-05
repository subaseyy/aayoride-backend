<?php

namespace Modules\UserManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface EmployeeInterface extends BaseRepositoryInterface
{
    public function trashed(array $attributes);

    public function restore(string $id);

    public function permanentDelete(string $id);
}

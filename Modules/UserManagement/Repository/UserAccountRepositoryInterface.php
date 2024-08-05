<?php

namespace Modules\UserManagement\Repository;

use App\Repository\EloquentRepositoryInterface;

interface UserAccountRepositoryInterface extends EloquentRepositoryInterface
{
    public function updateManyWithIncrement(array $ids, $column, $amount = 0);

}

<?php

namespace Modules\UserManagement\Service\Interface;

use App\Service\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerAccountServiceInterface extends BaseServiceInterface
{
    public function export(Collection $data): Collection|LengthAwarePaginator|\Illuminate\Support\Collection;

}

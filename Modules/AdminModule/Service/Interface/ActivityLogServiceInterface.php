<?php

namespace Modules\AdminModule\Service\Interface;

use App\Service\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ActivityLogServiceInterface extends BaseServiceInterface
{
    public function log(array $data): Collection|LengthAwarePaginator;
}

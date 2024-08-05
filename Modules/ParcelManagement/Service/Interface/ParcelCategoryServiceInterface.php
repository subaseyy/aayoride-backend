<?php

namespace Modules\ParcelManagement\Service\Interface;

use App\Service\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ParcelCategoryServiceInterface extends BaseServiceInterface
{
    public function export(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null): Collection|LengthAwarePaginator|\Illuminate\Support\Collection;
}

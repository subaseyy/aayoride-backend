<?php

namespace Modules\ZoneManagement\Service\Interface;

use App\Service\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ZoneServiceInterface extends BaseServiceInterface
{
    public function getZones(array $criteria = []): array;
    public function export(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null): Collection|LengthAwarePaginator|\Illuminate\Support\Collection;
    public function getByPoints($point);
}

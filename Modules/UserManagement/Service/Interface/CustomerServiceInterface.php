<?php

namespace Modules\UserManagement\Service\Interface;

use App\Service\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerServiceInterface extends BaseServiceInterface
{
    public function show(int|string $id, array $data);

    public function loyalCustomerCount($loyalLevelId);

    public function export(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator|\Illuminate\Support\Collection;
    public function changeLanguage(int|string $id, array $data = []): ?Model;

}

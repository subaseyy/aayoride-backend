<?php

namespace Modules\UserManagement\Service;

use App\Repository\EloquentRepositoryInterface;
use App\Service\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Repository\RoleRepositoryInterface;
use Modules\UserManagement\Service\Interface\EmployeeRoleServiceInterface;

class EmployeeRoleService extends BaseService implements Interface\EmployeeRoleServiceInterface
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        parent::__construct($roleRepository);
        $this->roleRepository = $roleRepository;
    }

    public function export(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator|\Illuminate\Support\Collection
    {
        return $this->index(criteria: $criteria, relations: $relations, orderBy: $orderBy)->map(function ($item) {
            return [
                'id' => $item['id'],
                'Name' => $item['name'],
                'Modules' => json_encode($item->modules),
                'Status' => $item['is_active'] ? 'Active' : 'Inactive'
            ];
        });
    }
}

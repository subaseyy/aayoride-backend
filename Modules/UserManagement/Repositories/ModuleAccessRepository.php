<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Entities\ModuleAccess;
use Modules\UserManagement\Interfaces\ModuleAccessInterface;

class ModuleAccessRepository implements ModuleAccessInterface
{
    public function __construct(
        private ModuleAccess $access
    )
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        // TODO: Implement get() method.
    }

    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        // TODO: Implement getBy() method.
    }

    public function store(array $attributes): Model
    {
        // TODO: Implement store() method.
    }

    public function update(array $attributes, string $id): Model
    {
        // TODO: Implement update() method.
    }

    public function destroy(string $id): Model
    {
        // TODO: Implement destroy() method.
    }
}

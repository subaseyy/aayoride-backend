<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Entities\Role;
use Modules\UserManagement\Interfaces\EmployeeRoleInterface;

class EmployeeRoleRepository implements EmployeeRoleInterface
{
    public function __construct(
        private Role $role
    )
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $value =  array_key_exists('value', $attributes)? $attributes['value'] : 'all';
        $search = array_key_exists('search', $attributes)? $attributes['search'] : '';

        $query = $this->role
            ->query()
            ->when($value != 'all', function ($query) use($attributes){
                $query->where($attributes['query'], $attributes['value']);
            })
            ->when($search, function ($query) use($search){
                $query->where(function ($query)use($search){
                    $keys = explode(' ', $search);
                    foreach ($keys as $key) {
                        $query->where('name', 'like', '%'. $key. '%');
                    }
                });

            })
            ->latest();

        if ($dynamic_page) {
            return $query->paginate($limit, ['*'], $offset);
        }

        return $query->paginate($limit);
    }

    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        return $this->role->query()->where($column, $value)->firstOrFail();
    }

    public function store(array $attributes): Model
    {
        $role = $this->role;
        $role->name = $attributes['name'];
        $role->modules = $attributes['modules'];
        $role->save();

        return $role;
    }

    public function update(array $attributes, string $id): Model
    {
        $role = $this->getBy(column: 'id', value: $id);
        if (array_key_exists('status', $attributes)){
            $role->is_active = $attributes['status'];
        }
         else {
             $role->name = $attributes['name'];
             $role->modules = $attributes['modules'];
         }
        $role->save();
        return $role;
    }

    public function destroy(string $id): Model
    {
        $role = $this->getBy(column: 'id', value: $id);
        $role->delete();
        return $role;
    }
}

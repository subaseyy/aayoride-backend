<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Entities\UserAddress;
use Modules\UserManagement\Entities\WithdrawMethod;
use Modules\UserManagement\Interfaces\AddressInterface;
use Modules\UserManagement\Interfaces\WithdrawalMethodInterface;

class WithdrawalMethodRepository implements WithdrawalMethodInterface
{
    public function __construct(
        private WithdrawMethod $method)
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $search = array_key_exists('search', $attributes)? $attributes['search'] : null;
        $value =  array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column =  array_key_exists('query', $attributes) ? $attributes['query'] : '';
        $queryParam = ['search' => $search, 'query' => $column, 'value' => $value];

        $query = $this->method
            ->when($search, function ($query) use ($attributes) {
                $keys = explode(' ', $attributes['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->where('method_name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($value!= 'all', fn($query) =>
                $query->where($column,  $value)
            )
            ->latest();

        if (!$dynamic_page) {
            return $query->paginate(paginationLimit())->appends($queryParam);
        }

        return $query->paginate(perPage: $limit, page: $offset);
    }

    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        return $this->method->query()->where($column, $value)
        ->when(array_key_exists('column', $attributes), fn($query) =>
            $query->where($attributes['column'], $attributes['value'])
        )
        ->first();
    }

    public function store(array $attributes): Model
    {
        $method = $this->method;
        $method->method_name = $attributes['method_name'];
        $method->method_fields = $attributes['method_fields'];
        $method->is_default = $attributes['is_default'];
        $method->save();

        return $method;
    }

    public function update(array $attributes, string $id): Model
    {
        $method = $this->method
            ->where($attributes['column'], $id)
            ->first();
        $method->is_default = $attributes['is_default'];
        $method->save();

        return $method;

    }

    public function destroy(string $id): Model
    {
        $method = $this->method->where('id', $id)->first();
        $method->delete();

        return $method;
    }

    public function AjaxDefaultStatusUpdate($id)
    {
         $method = $this->method
            ->where('id', $id)
            ->first();
        $success = 1;
        if ($method->is_default == 1) {
            $success = 0;
        } else {
            $this->method->where('id', $id)->update(['is_default' => !$method->is_default, 'is_active' => 1]);
            $this->method->where('id', '!=', $id)->update(['is_default' => $method->is_default]);
        }
        return $success;
    }

    public function AjaxActiveStatusUpdate($id)
    {
        $status = 1;
        $method = $this->method->where('id', $id)->first();
        if ($method->is_default == 1) {
            $status = 0;
        } else {
            $method->is_active = ($method['is_active'] == 0 || $method['is_active'] == null) ? 1 : 0;
            $method->save();
        }
        return $status;
    }
}

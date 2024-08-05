<?php

namespace Modules\UserManagement\Repositories;

use App\CPU\Convert;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\UserManagement\Entities\UserAddress;
use Modules\UserManagement\Entities\WithdrawMethod;
use Modules\UserManagement\Entities\WithdrawRequest;
use Modules\UserManagement\Interfaces\AddressInterface;
use Modules\UserManagement\Interfaces\WithdrawalMethodInterface;
use Modules\UserManagement\Interfaces\WithdrawRequestInterface;

class WithdrawRequestRepository implements WithdrawRequestInterface
{
    public function __construct(
        private WithdrawRequest $request)
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $search = array_key_exists('search', $attributes)? $attributes['search'] : null;
        $value =  array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column =  array_key_exists('query', $attributes) ? $attributes['query'] : '';
        $queryParam = ['search' => $search, 'query' => $column, 'value' => $value];

        $query = $this->request
            ->when($search, function ($query) use ($attributes) {
                $keys = explode(' ', $attributes['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->where('request_name', 'LIKE', '%' . $key . '%');
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
        return $this->request->query()->where($column, $value)
        ->when(array_key_exists('column', $attributes), fn($query) =>
            $query->where($attributes['column'], $attributes['value'])
        )
            ->when(array_key_exists('relations', $attributes), fn($query) =>
            $query->with($attributes['relations'])
        )
        ->first();
    }

    public function store(array $attributes): Model
    {
        $request = $this->request;
        $request->user_id = $attributes['user_id'];
        $request->amount = $attributes['amount'];
        $request->method_id = $attributes['method_id'];
        $request->method_fields = $attributes['method_fields'];
        array_key_exists('note', $attributes) ? $request->note = $attributes['note'] : null;
        $request->save();

        return $request;
    }

    public function update(array $attributes, string $id): Model
    {
        $request = $this->request->where($attributes['column'], $id)->first();
        $request->is_approved = $attributes['is_approved'];
        if (array_key_exists('rejection_cause', $attributes) && $request->is_approved == 0) {
            $request->rejection_cause = $attributes['rejection_cause'];
        }
        $request->save();

        return $request;

    }

    public function destroy(string $id): Model
    {
        $request = $this->request->where('id', $id)->first();
        $request->delete();

        return $request;
    }

}

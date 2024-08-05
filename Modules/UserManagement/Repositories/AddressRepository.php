<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Entities\UserAddress;
use Modules\UserManagement\Interfaces\AddressInterface;

class AddressRepository implements AddressInterface
{
    public function __construct(
        private UserAddress $address)
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $search = array_key_exists('search', $attributes)? $attributes['search'] : '';
        $value =  array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column =  array_key_exists('query', $attributes) ? $attributes['query'] : '';
        $queryParam = ['search' => $search, 'query' => $column, 'value' => $value];

        $query = $this->address
            ->query()
            ->when(!empty($relations[0]), fn ($query) => $query->with($relations))
            ->when($search, function ($query) use ($attributes) {
                $keys = explode(' ', $attributes['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->where('name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($column && $value != 'all', fn($query) =>
                $query->where($column,($value=='active'?1:($value == 'inactive'?0:$value))))
            ->when(!empty($except[0]), fn($query) => $query->whereNotIn('id', $except))
            ->latest();

        if (!$dynamic_page) {

            return $query->paginate(paginationLimit())->appends($queryParam);
        }

        return $query->paginate($limit, ['*'], $offset);
    }

    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        return $this->address
            ->query()
            ->where($column, $value)
            ->when(array_key_exists('column', $attributes), fn($query) =>
                $query->whereIn($attributes['column'], $attributes['value'])
            )
            ->first();
    }

    public function store(array $attributes): Model
    {
        $address = $this->address;
        $address->user_id = $attributes['user_id'];
        $address->latitude = $attributes['latitude'] ?? null;
        $address->longitude = $attributes['longitude'] ?? null;
        $address->city = $attributes['city'] ?? null;
        $address->street = $attributes['street'] ?? null;
        $address->zip_code = $attributes['zip_code'] ?? null;
        $address->country = $attributes['country'] ?? null;
        $address->address = $attributes['address'];
        $address->address_label = $attributes['address_label'] ?? null;
        $address->zone_id = $attributes['zone_id'] ?? null;
        $address->save();

        return $address;
    }

    public function update(array $attributes, string $id): Model
    {
        $address = $this->getBy(column: 'id', value: $id);
        $address->user_id = $attributes['user_id'];
        $address->latitude = $attributes['latitude'] ?? null;
        $address->longitude = $attributes['longitude'] ?? null;
        $address->city = $attributes['city']??null;
        $address->street = $attributes['street']??null;
        $address->zip_code = $attributes['zip_code']??null;
        $address->country = $attributes['country']??null;
        $address->address = $attributes['address'];
        $address->address_label = $attributes['address_label'] ?? null;
        $address->save();

        return $address;

    }

    public function destroy(string $id): Model
    {
        $customer = $this->getBy(column: 'id', value: $id);
        $customer->delete();

        return $customer;
    }
}

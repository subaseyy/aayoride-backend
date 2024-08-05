<?php

namespace Modules\TripManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\TripManagement\Entities\RecentAddress;
use Modules\TripManagement\Interfaces\RecentAddressInterface;

class RecentAddressRepository implements RecentAddressInterface
{
    public function __construct(
        private RecentAddress $address
    )
    {

    }

    /**
     * @param int $limit
     * @param int $offset
     * @param bool $dynamic_page
     * @param array $except
     * @param array $attributes
     * @param array $relations
     * @return LengthAwarePaginator|array|Collection
     */
    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $column = $attributes['column'] ?? null;
        $value = $attributes['value'] ?? null;

        $query =  $this->address
            ->query()
            ->when(!empty($relations[0]), function ($query) use ($relations){
                $query->with($relations);
            })
            ->when(array_key_exists('zone_id', $attributes), function($query) use($attributes, $column, $value){
                $query->where($column,$value)->where('zone_id', $attributes['zone_id']);
            })
            ->when(!array_key_exists('zone_id', $attributes), function($query) use($column, $value){
                $query->where($column, $value);
            });
            if (!$dynamic_page) {
                return $query->latest()->paginate($limit);
            } else {
                return $query->latest()->paginate($limit, ['*'],'page', $offset);
            }
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return mixed|Model
     */
    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        // TODO: Implement getBy() method.
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {
        $address = $this->address;
        $address->user_id = $attributes['user_id'];
        $address->zone_id = $attributes['zone_id'];
        $address->pickup_coordinates = $attributes['pickup_coordinates'];
        $address->pickup_address = $attributes['pickup_address'];
        $address->destination_coordinates = $attributes['destination_coordinates'];
        $address->destination_address = $attributes['destination_address'];

        $address->save();
        return $address;
    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model
    {
        // TODO: Implement update() method.
    }

    /**
     * @param string $id
     * @return Model
     */
    public function destroy(string $id): Model
    {
        // TODO: Implement destroy() method.
    }
}

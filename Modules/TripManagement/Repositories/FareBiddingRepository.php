<?php

namespace Modules\TripManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\TripManagement\Entities\FareBidding;
use Modules\TripManagement\Interfaces\FareBiddingInterface;

class FareBiddingRepository implements FareBiddingInterface
{
    public function __construct(private FareBidding $fare_bidding)
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
        $query = $this->fare_bidding
            ->query()
            ->when(array_key_exists('relations', $attributes), function ($query) use ($attributes){
                $query->with($attributes['relations']);
            })
            ->when(array_key_exists('withAvgRelation', $attributes), function ($query) use($attributes){
                $query->withAvg($attributes['withAvgRelation'], $attributes['withAvgColumn']);
            })
            ->when(array_key_exists('trip_request_id', $attributes), function ($query) use ($attributes){
                $query->where('trip_request_id', $attributes['trip_request_id']);
            })
            ->when(array_key_exists('without_ids', $attributes), function ($query) use ($attributes){
                $query->whereNotIn('id', $attributes['without_ids']);
            })
            ->ofIsNotIgnored();

        if($dynamic_page) {
            return $query->paginate(perPage: $limit, page: $offset);
        }
        return $query->get();
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return mixed|Model
     */
    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        return $this->fare_bidding
            ->query()
            ->where($column, $value)
            ->when(array_key_exists('additionalColumn', $attributes), function ($query) use($attributes){
                $query->where($attributes['additionalColumn'], $attributes['additionalValue']);
            })
            ->when(array_key_exists('additionalColumn2', $attributes), function ($query) use($attributes){
                $query->where($attributes['additionalColumn2'], $attributes['additionalValue2']);
            })
            ->first();
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {
        $bid = $this->fare_bidding;
        array_key_exists('trip_request_id', $attributes) ? $bid->trip_request_id = $attributes['trip_request_id'] : null;
        array_key_exists('driver_id', $attributes) ? $bid->driver_id = $attributes['driver_id'] : null;
        array_key_exists('customer_id', $attributes) ? $bid->customer_id = $attributes['customer_id'] : null;
        array_key_exists('bid_fare', $attributes) ? $bid->bid_fare = $attributes['bid_fare'] : null;
        array_key_exists('is_accepted', $attributes) ? $bid->is_accepted = $attributes['is_accepted'] : null;
        $bid->save();

        return $bid;
        // TODO: Implement store() method.
    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model
    {
        $bid = $this->fare_bidding->query()->where($attributes['column'], $id)->first();
        array_key_exists('is_ignored', $attributes) ? $bid->is_ignored = $attributes['is_ignored'] : null;
//        array_key_exists('is_accepted', $attributes) ? $bid->is_accepted = $attributes['is_accepted'] : null;
        array_key_exists('bid_fare', $attributes) ? $bid->bid_fare = $attributes['bid_fare'] : null;
        $bid->save();

        return $bid;
    }

    /**
     * @param string $id
     * @return Model
     */
    public function destroy(string $id): Model
    {
        $fare_bidding = $this->getBy(column: 'id', value: $id);
        $fare_bidding->delete();
        return $fare_bidding;
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function destroyData($attributes): mixed
    {
        return $this->fare_bidding->query()->whereIn($attributes['column'], $attributes['ids'])->delete();
    }
}

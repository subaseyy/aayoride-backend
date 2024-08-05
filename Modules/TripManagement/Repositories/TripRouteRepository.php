<?php

namespace Modules\TripManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\TripManagement\Entities\TripRoute;
use Modules\TripManagement\Interfaces\TripRouteInterface;

class TripRouteRepository implements TripRouteInterface
{

    public function __construct(private TripRoute $trip_route)
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
        $query =  $this->trip_route
            ->query()
            ->when(!array_key_exists('column', $attributes), function($query) use($attributes){
                $query->where($attributes['column'], $attributes['value']);
            });
        if (!$dynamic_page) {

            return $query->latest()->paginate(perPage: $limit, page: $offset);
        }

        return $query->latest()->get();
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
        $trip_route = $this->trip_route;
        $trip_route->trip_request_id = $attributes['trip_request_id'];
        $trip_route->coordinates = $attributes['coordinates'];
        $trip_route->save();

        return $trip_route;
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

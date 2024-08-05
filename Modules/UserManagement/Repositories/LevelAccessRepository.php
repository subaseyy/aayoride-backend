<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Entities\LevelAccess;
use Modules\UserManagement\Interfaces\LevelAccessInterface;

class LevelAccessRepository implements LevelAccessInterface
{
    public function __construct(
        private LevelAccess $levelAccess
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
    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes= [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        return [];
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return Model
     */
    public function getBy(string $column, string|int $value, array $attributes = []): Model
    {
        return $this->levelAccess
            ->query()
            ->where($column, $value)
            ->firstOrFail();
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {

    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model
    {
        return $this->levelAccess
            ->query()
            ->updateOrCreate(['level_id' => $attributes['id']], [
                $attributes['name'] => $attributes['value'],
                'user_type' => $attributes['user_type'] == DRIVER ?? CUSTOMER
            ]);
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

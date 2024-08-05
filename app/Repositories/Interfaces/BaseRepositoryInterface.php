<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BaseRepositoryInterface {

    /**
     * Returns Collection of Records
     * @param int $limit
     * @param int $offset
     * @param bool $dynamic_page
     * @param array $except
     * @param array $attributes
     * @param array $relations
     * @return LengthAwarePaginator|array|Collection
     */
    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes= [], array $relations = []): LengthAwarePaginator|array|Collection;

    /**
     * Returns Single Records
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return Model
     */
    public function getBy(string $column, string|int $value, array $attributes = []): mixed;

    /**
     * Stores a Record
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model;


    /**
     * Update a Record
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model;

    /**
     * Deletes a Record
     * @param string $id
     * @return Model
     */
    public function destroy(string $id);
}

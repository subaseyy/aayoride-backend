<?php

namespace Modules\UserManagement\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface TimeTrackInterface
{
    /**
     * @param int $limit
     * @param int $offset
     * @param bool $dynamic_page
     * @param array $except
     * @param array $attributes
     * @param array $relations
     * @return LengthAwarePaginator|array|Collection
     */
    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection;

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return mixed
     */
    public function getBy(string $column, string|int $value, array $attributes = []): mixed;
}

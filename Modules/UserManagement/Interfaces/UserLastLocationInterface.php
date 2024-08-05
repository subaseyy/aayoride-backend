<?php

namespace Modules\UserManagement\Interfaces;

interface UserLastLocationInterface{

    /**
     * Returns Collection of Records
     * @param int $limit
     * @param bool $dynamic_page
     * @param array $except
     * @param array $attributes
     * @param array $relations
     * @return mixed
     */
    public function get(int $limit, bool $dynamic_page = false, array $except = [], array $attributes= [], array $relations = []): mixed;

    /**
     * @param string $column
     * @param int|string $value
     * @param array $attributes
     * @return mixed
     */
    public function getBy(string $column, int|string $value, array $attributes = []):mixed;

    /**
     * @param $attributes
     * @return mixed
     */
    public function updateOrCreate($attributes):mixed;

    /**
     * @param $attributes
     * @return mixed
     */
    public function getNearestDrivers($attributes):mixed;

}

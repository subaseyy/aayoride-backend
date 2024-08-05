<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Entities\TimeTrack;
use Modules\UserManagement\Interfaces\TimeTrackInterface;

class TimeTrackRepository implements TimeTrackInterface
{
    public function __construct(
        private TimeTrack $track
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
        $from = $attributes['from'] ?? null;
        $to = $attributes['to'] ?? null;
        $column = $attributes['column'] ?? null;
        $value = $attributes['value'] ?? null;

        $query = $this->track
            ->query()
            ->when(array_key_exists('relations', $attributes), fn ($query) => $query->with($attributes['relations']))
            ->when($from && $to, fn ($query) => $query->whereBetween('date', [$from, $to]))
            ->when($column && $value, fn ($query) => $query->where($column, $value));

        if ($dynamic_page) {

            return $query->paginate(perPage: $limit,  page: $offset);
        }
        return $query->paginate($limit);
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return Model
     */
    public function getBy(string $column, string|int $value, array $attributes = []): mixed
    {
        $id = $attributes['id'] ?? null;
        $date = $attributes['date'] ?? null; //expecting today or yesterday
        return $this->track->query()
            ->when(array_key_exists('relations', $attributes), fn ($query) =>
                $query->with($attributes['relations'])
            )
            ->when($id && $date, fn ($query) =>
                $query->where(['user_id' => $id, date('Y-m-d', strtotime($date))])
            )
            ->latest()
            ->first();
    }


}

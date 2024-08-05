<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Entities\LoyaltyPointsHistory;
use Modules\UserManagement\Entities\UserAddress;
use Modules\UserManagement\Interfaces\AddressInterface;
use Modules\UserManagement\Interfaces\LoyaltyPointsHistoryInterface;
use function RingCentral\Psr7\parse_query;

class LoyaltyPointsHistoryRepository implements LoyaltyPointsHistoryInterface
{
    public function __construct(
        private LoyaltyPointsHistory $history)
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $query = $this->history
            ->query()
            ->when(array_key_exists('column', $attributes), fn ($query) =>
                $query->where($attributes['column'], $attributes['value'])
            )
            ->latest();
        if (!$dynamic_page) {
            return $query->paginate(paginationLimit());
        }

        return $query->paginate(perPage: $limit, page: $offset);
    }

    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        return $this->history->query()->where($column, $value)
        ->when(array_key_exists('column', $attributes) && array_key_exists('value', $attributes), function($query) use($attributes){
            $query->whereIn($attributes['column'], $attributes['value']);
        })
        ->first();
    }

    public function store(array $attributes): Model
    {
        $history = $this->history;
        $history->user_id = $attributes['user_id'];
        $history->model = $attributes['model'];
        $history->model_id = $attributes['model_id'];
        $history->points = $attributes['points'];
        $history->type = $attributes['type'];
        $history->save();

        return $history;
    }

    public function update(array $attributes, string $id): Model
    {
        //
    }

    public function destroy(string $id): Model
    {
        //
    }
}

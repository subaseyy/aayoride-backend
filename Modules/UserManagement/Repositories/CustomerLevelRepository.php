<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Entities\UserLevel;
use Modules\UserManagement\Interfaces\CustomerLevelInterface;

class CustomerLevelRepository implements CustomerLevelInterface
{

    public function __construct(
        protected UserLevel $level
    )
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes= [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $search = array_key_exists('search', $attributes)? $attributes['search'] : '';
        $value =  array_key_exists('value', $attributes)? $attributes['value'] : 'all';
        $column = array_key_exists('query', $attributes)? $attributes['query'] : '';
        $queryParams = ['value' => $value, 'search' => $search, 'column' => $column];

        $query = $this->level
            ->query()
            ->userType(CUSTOMER)
            ->when($search, function ($query) use($search){
                $query->where(function ($query) use($search){
                    $keys = explode(' ', $search);
                    foreach ($keys as $key) {
                        $query->where('name', 'like', '%'.$key.'%')
                            ->orWhere('id', 'like', '%'.$key.'%');
                    }
                });
            })
            ->when($value!= 'all', function ($query) use($column, $value){
                $query->where($column,  $value);
            })->when(array_key_exists('orderBy', $attributes), function ($query) use($attributes){
                $query->orderBy('sequence', $attributes['orderBy']);
            })
            ->latest();

        if ($dynamic_page) {
            return $query->paginate($limit, ['*'], $offset);
        }

        return $query->paginate($limit)
            ->appends($queryParams);
    }

    public function getBy(string $column, string|int $value, array $attributes = []): Model
    {
        return $this->level
            ->query()
            ->when($attributes['withCount'] ?? null, fn($query) => $query->withCount($attributes['withCount']))
            ->where($column, $value)
            ->first();
    }

    public function store(array $attributes): Model
    {
        $level = $this->level;
        $level->sequence = $attributes['sequence'];
        $level->name = $attributes['name'];
        $level->reward_type = $attributes['reward_type'];
        if ($attributes['reward_type'] != 'no_rewards') {
            $level->reward_amount = $attributes['reward_amount'];
        }
        $level->targeted_ride = $attributes['targeted_ride'] ?? 0;
        $level->targeted_ride_point = $attributes['targeted_ride_point'] ?? 0;
        $level->targeted_amount = $attributes['targeted_amount'] ?? 0;
        $level->targeted_amount_point = $attributes['targeted_amount_point'] ?? 0;
        $level->targeted_cancel = $attributes['targeted_cancel'] ?? 0;
        $level->targeted_cancel_point = $attributes['targeted_cancel_point'] ?? 0;
        $level->targeted_review = $attributes['targeted_review'] ?? 0;
        $level->targeted_review_point = $attributes['targeted_review_point'] ?? 0;
        $level->image = fileUploader('customer/level/', 'png', $attributes['image']);
        $level->user_type = CUSTOMER;
        $level->save();

        return $level;
    }

    public function update(array $attributes, string $id): Model
    {
        $level = $this->getBy(column: 'id', value: $id);

        if (!array_key_exists('status', $attributes)){
            $level->name = $attributes['name'];
            $level->targeted_ride = $attributes['targeted_ride'] ?? 0;
            $level->targeted_ride_point = $attributes['targeted_ride_point'] ?? 0;
            $level->targeted_amount = $attributes['targeted_amount'] ?? 0;
            $level->targeted_amount_point = $attributes['targeted_amount_point'] ?? 0;
            $level->targeted_cancel = $attributes['targeted_cancel'] ?? 0;
            $level->targeted_cancel_point = $attributes['targeted_cancel_point'] ?? 0;
            $level->targeted_review = $attributes['targeted_review'] ?? 0;
            $level->targeted_review_point = $attributes['targeted_review_point'] ?? 0;
            if (array_key_exists('image', $attributes)) {
                $level->image = fileUploader('customer/level/', 'png', $attributes['image'], $level->image);
            }
            $level->user_type = CUSTOMER;
        } else {
            $level->is_active = $attributes['status'];
        }
        $level->save();

        return $level;
    }

    public function destroy(string $id): Model
    {
        $level = $this->getBy(column: 'id', value: $id);
        $level->delete();
        return $level;
    }

    /**
     * For Admin panels Driver Level Data
     * @param array $attributes
     * @param bool $export
     * @return mixed
     */
    public function getLevelizedTrips(array $attributes, $export = false): mixed
    {
        $search = array_key_exists('search', $attributes)? $attributes['search'] : '';
        $value =  array_key_exists('value', $attributes)? $attributes['value'] : 'all';
        $column = array_key_exists('query', $attributes)? $attributes['query'] : '';
        $queryParams = ['value' => $value, 'search' => $search, 'column' => $column];
        $query = $this->level->query()
            ->when($search, function ($query) use($search){
                $query->where(function ($query) use($search){
                    $keys = explode(' ', $search);
                    foreach ($keys as $key) {
                        $query->where('name', 'like', '%'.$key.'%')
                            ->orWhere('id', 'like', '%'.$key.'%');
                    }
                });
            })
            ->when(!empty($attributes['dates']), function ($query) use($attributes){
                $query->whereBetween('created_at', [$attributes['dates']['start'], $attributes['dates']['end']]);
            })
            ->when($value!= 'all', function ($query) use($column, $value){
                $query->where('is_active',  $value);
            })
            ->with(['users.customerTrips'])
            ->withCount('users')
            ->where('user_type', 'customer');
        if ($export) {

            return $query->get();
        }

        return $query->paginate(paginationLimit())->appends($queryParams);
    }

    public function getFirstLevel(): mixed
    {
        return $this->level->query()
            ->userType(CUSTOMER)
            ->orderBy('created_at')
            ->first();
    }

    public function trashed(array $attributes)
    {
        $search = $attributes['search'] ?? null;
        $relations = $attributes['relations'] ?? null;
        return $this->level->query()
        ->when($relations, function ($query) use ($relations){
            $query->with($relations);
        })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $keys = explode(' ', $search);
                    foreach ($keys as $key) {
                        $query->where('first_name', 'like', '%'. $key. '%')
                            ->orWhere('last_name', 'like', '%'. $key. '%')
                            ->orWhere('phone', 'like', '%'. $key. '%')
                            ->orWhere('email', 'like', '%'. $key. '%');
                    }
                });
            })
            ->with(['users.customerTrips'])
            ->withCount('users')
            ->userType(CUSTOMER)
            ->onlyTrashed()
            ->paginate(paginationLimit());

    }

    public function restore(string $id)
    {
        return $this->level->query()->userType(CUSTOMER)->onlyTrashed()->find($id)->restore();
    }

    public function permanentDelete(string $id): Model
    {
        $model = $this->level->query()->userType(CUSTOMER)->onlyTrashed()->find($id);
        $model->forceDelete();
        return $model;
    }

}

<?php

namespace Modules\UserManagement\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\AdminModule\Entities\ActivityLog;
use Modules\UserManagement\Entities\UserLevel;
use Modules\UserManagement\Interfaces\DriverLevelInterface;

class DriverLevelRepository implements DriverLevelInterface
{

    public function __construct(
        protected UserLevel $level,
        protected ActivityLog $log
    )
    {
    }
    /**
     * Summary of get
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
        $search = array_key_exists('search', $attributes)? $attributes['search'] : '';
        $value =  array_key_exists('value', $attributes)? $attributes['value'] : 'all';
        $column = array_key_exists('query', $attributes)? $attributes['query'] : '';
        $queryParams = ['value' => $value, 'search' => $search, 'column' => $column];

        $query = $this->level
            ->query()
            ->userType(DRIVER)
            ->when(array_key_exists('relations', $attributes), function($query) use($attributes){
                $query->with('users');
            })
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
            })
            ->latest();

        if ($dynamic_page) {
            return $query->paginate(perPage: $limit, page: $offset);
        }

        return $query->paginate(paginationLimit())
            ->appends($queryParams);
    }
    /**
     * Summary of getBy
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return Model
     */
    public function getBy(string $column, string|int $value, array $attributes = []): Model
    {
        return $this->level
            ->query()
            ->where($column, $value)
            ->when($attributes['withCount'] ?? null, fn($query) => $query->withCount($attributes['withCount']))
            ->firstOrFail();
    }

    /**
     * Summary of store
     * @param array $attributes
     * @return Model
     */
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
        $level->image = fileUploader('driver/level/', 'png', $attributes['image']);
        $level->user_type = DRIVER;

        $level->save();

        return $level;
    }
    /**
     * Summary of update
     * @param array $attributes
     * @param string $id
     * @return Model
     */
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
                $level->image = fileUploader('driver/level/', 'png', $attributes['image'], $level->image);
            }
        } else {
            $level->is_active = $attributes['status'];
        }
            $level->save();
        return $level;
    }

    /**
     * Summary of destroy
     * @param string $id
     * @return Model
     */
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
            ->with(['users.driverTrips', 'users.driverTripsStatus']
            )
            ->where('user_type', DRIVER);
        if ($export) {

            return $query->get();
        }

        return $query->paginate(paginationLimit())->appends($queryParams);
    }

    /**
     * Summary of getFirstLevel
     * @return mixed
     */
    public function getFirstLevel(): mixed
    {
        return $this->level->query()
            ->userType(DRIVER)
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
            ->with(['users.driverTrips'])
            ->userType(DRIVER)
            ->onlyTrashed()
            ->paginate(paginationLimit());

    }

    public function restore(string $id)
    {
        return $this->level->query()->userType(DRIVER)->onlyTrashed()->find($id)->restore();
    }

    public function permanentDelete(string $id): Model
    {
        $model = $this->level->query()->userType(DRIVER)->onlyTrashed()->find($id);
        $model->forceDelete();
        return $model;
    }

}

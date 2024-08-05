<?php

namespace Modules\ParcelManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\ParcelManagement\Entities\ParcelWeight;
use Modules\ParcelManagement\Interfaces\ParcelWeightInterface;


class ParcelWeightRepository implements ParcelWeightInterface
{
    private $weight;

    public function __construct(ParcelWeight $weight)
    {
        $this->weight = $weight;
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
        $search = array_key_exists('search', $attributes)? $attributes['search'] : '';
        $value =  array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column =  array_key_exists('query', $attributes) ? $attributes['query'] : '';

        $queryParam = ['search' => $search, 'query' => $column, 'value' => $value];

        $query = $this->weight
            ->query()
            ->when(!empty($relations[0]), function ($query) use ($relations){
                $query->with($relations);
            })
            ->when($search, function ($query) use ($attributes) {
                $keys = explode(' ', $attributes['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->where('min_weight', '<=', $key)->where('max_weight', '>=', $key);
                    }
                });
            })
            ->when($column && $value != 'all', function($query) use($column, $value){
                return $query->where($column,($value=='active'?1:($value == 'inactive'?0:$value)));
            })
            ->when(!empty($except[0]), function ($query) use($except){
                $query->whereNotIn('id', $except);
            })->orderBy('min_weight');

        if (!$dynamic_page) {
            return $query->latest()->paginate($limit)->appends($queryParam);
        }

        return $query->latest()->paginate($limit, ['*'], $offset);
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return mixed|Model
     */
    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        return $this->weight->where([$column => $value])->firstOrFail();
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {

        $model = $this->weight;
        $model->min_weight = $attributes['minimum_weight'];
        $model->max_weight = $attributes['maximum_weight'];
        $model->save();

        return $model;
    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model
    {
        $model = $this->getBy(column: 'id', value: $id);

        if(!array_key_exists('status', $attributes)){
            $key_exist = $this->weight->where('id','<>',$id)->where('max_weight', '>=', $attributes['minimum_weight'])->where('min_weight', '<=', $attributes['maximum_weight'])->first();
            if(!$key_exist){
                $model->min_weight = $attributes['minimum_weight'];
                $model->max_weight = $attributes['maximum_weight'];
            }else{
                abort(403, message:'Weight already exist.');
            }
        }else{
            $model->is_active = $attributes['status'];
        }

        $model->save();

        return $model;    }

    /**
     * @param string $id
     * @return Model
     */
    public function destroy(string $id): Model
    {
        $model = $this->getBy(column: 'id', value: $id);
        $model->delete();
        return $model;
    }

    /**
    * Download functionalities
    * @param array $attributes
    * @return mixed
    */
    public function download(array $attributes = []): mixed
    {
        $search = array_key_exists('search', $attributes)? $attributes['search'] : '';
        $value =  array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column =  array_key_exists('query', $attributes) ? $attributes['query'] : '';

        $model = $this->weight->query()
        ->when($search, function ($query) use ($attributes) {
            $keys = explode(' ', $attributes['search']);
            return $query->where(function ($query) use ($keys) {
                foreach ($keys as $key) {
                    $query->where('min_weight', '<=', $key)->where('max_weight', '>=', $key);
                }
            });
        })
        ->when($column && $value != 'all', function($query) use($column, $value){
            return $query->where($column,($value=='active'?1:($value == 'inactive'?0:$value)));
        })
        ->latest()->get();

        return $model;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function trashed(array $attributes)
    {
        $search = $attributes['search'] ?? null;
        return $this->weight->query()
            ->when($search, function ($query) use ($search) {
                $keys = explode(' ', $search);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->where('min_weight', '<=', $key)->where('max_weight', '>=', $key);
                    }
                });
            })
            ->onlyTrashed()
            ->paginate(paginationLimit())
            ->appends(['search' => $search]);

    }

    /**
     * @param string $id
     * @return mixed
     */

    public function restore(string $id)
    {
        return $this->weight->query()->onlyTrashed()->find($id)->restore();
    }

    public function permanentDelete(string $id): Model
    {
        $model = $this->weight->query()->onlyTrashed()->find($id);
        $model->forceDelete();
        return $model;
    }

}

<?php

namespace Modules\VehicleManagement\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\VehicleManagement\Entities\VehicleBrand;
use Modules\VehicleManagement\Entities\VehicleCategory;
use Modules\VehicleManagement\Interfaces\VehicleCategoryInterface;

class VehicleCategoryRepository implements VehicleCategoryInterface{

    private VehicleCategory $category;

    public function __construct(VehicleCategory $category)
    {
        $this->category = $category;
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
    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes= [] , array $relations = []) :LengthAwarePaginator|array|Collection
    {
        $search = array_key_exists('search', $attributes)? $attributes['search'] : '';
        $value =  array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column =  array_key_exists('query', $attributes) ? $attributes['query'] : '';

        $relationalColumn = array_key_exists('column_name', $attributes) ? $attributes['column_name'] : '';
        $relationalColumnValue = array_key_exists('column_value', $attributes) ? $attributes['column_value'] : '';
        $operator = array_key_exists('operator', $attributes) ? $attributes['operator'] : null;
        $hasKey = array_key_exists('whereHas', $attributes) ? $attributes['whereHas'] : '';

        $queryParam = ['search' => $search, 'query' => $column, 'value' => $value];

        $query = $this->category
            ->query()
            ->when(!empty($relations[0]), function ($query) use ($relations){
                $query->with($relations);
            })
            ->when($search, function ($query) use ($attributes) {
                $keys = explode(' ', $attributes['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->where('name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($column && $value != 'all', function($query) use($column, $value){

                return $query->where($column,($value=='active'?1:($value == 'inactive'?0:$value)));
            })
            ->when($relationalColumn && $relationalColumnValue && $hasKey, function($query) use($relationalColumn, $relationalColumnValue, $hasKey, $operator){

                $query->whereHas($hasKey, function($query) use($relationalColumn, $relationalColumnValue, $operator){
                    $query->when(!is_null($operator), function($query)use($relationalColumn, $relationalColumnValue, $operator){
                        $query->where($relationalColumn,$operator, $relationalColumnValue);
                    })
                    ->when(is_null($operator), function($query)use($relationalColumn, $relationalColumnValue){
                        $query->where($relationalColumn, $relationalColumnValue);
                    });
                });
            })
            ->when(!empty($except[0]), function ($query) use($except){
                $query->whereNotIn('id', $except);
            });

        if ($attributes['get'] ?? null) {
            return $query->get();
        }
        if (!$dynamic_page) {
            return $query->latest()->paginate($limit)->appends($queryParam);
        } else {
            return $query->latest()->paginate($limit, ['*'], $offset);
        }
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return Model
     */

    public function getBy(string $column, string|int $value, array $attributes = []):mixed
    {
        return $this->category->where([$column => $value])->firstOrFail();
    }

    /**
     * @param array $attributes
     * @return Model
     */

    public function store(array $attributes):Model
    {
        $model = $this->category;
        $model->name = $attributes['category_name'];
        $model->description = $attributes['short_desc'];
        $model->type = $attributes['type'];
        $model->image = fileUploader('vehicle/category/', 'png',$attributes['category_image']);
        $model->save();

        return $model;
    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model
     */

    public function update(array $attributes, string $id):Model
    {

        $model = $this->getBy(column: 'id', value: $id);

        if(!array_key_exists('status', $attributes)){

            $model->name = $attributes['category_name'];
            $model->description = $attributes['short_desc'];
            $model->type = $attributes['type'];

            if(array_key_exists('category_image', $attributes)){
                $model->image = fileUploader('vehicle/category/', 'png',$attributes['category_image'], $model->image);

            }
        }else{
            $model->is_active = $attributes['status'];
        }

        $model->save();

        return $model;
    }

    /**
     * @param string $id
     * @return Model
     */

    public function destroy(string $id):Model
    {
        $model = $this->getBy(column: 'id', value: $id);
        $model->delete();
        return $model;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function trashed(array $attributes)
    {
        $search = $attributes['search'] ?? null;
        return $this->category->query()
            ->when($search, function ($query) use ($search) {
                $keys = explode(' ', $search);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->where('name', 'LIKE', '%' . $key . '%');
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
        return $this->category->query()->onlyTrashed()->find($id)->restore();
    }

    public function permanentDelete(string $id): Model
    {
        $model = $this->category->query()->onlyTrashed()->find($id);
        $model->forceDelete();
        return $model;
    }
}

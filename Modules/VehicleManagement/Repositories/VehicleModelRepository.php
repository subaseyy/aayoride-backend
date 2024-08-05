<?php

namespace Modules\VehicleManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\VehicleManagement\Entities\VehicleModel;
use Modules\VehicleManagement\Interfaces\VehicleModelInterface;

class VehicleModelRepository implements VehicleModelInterface{

    private $model;

    public function __construct(VehicleModel $model)
    {
        $this->model = $model;
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
    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes= [], array $relations = []) :LengthAwarePaginator|array|Collection
    {
        $search = array_key_exists('search', $attributes)? $attributes['search'] : '';
        $value =  array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column =  array_key_exists('query', $attributes) ? $attributes['query'] : '';

        $queryParam = ['search' => $search, 'query' => $column, 'value' => $value];

        $query = $this->model
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
            ->when(!empty($except[0]), function ($query) use($except){
                $query->whereNotIn('id', $except);
            });

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
     * @return mixed
     */

    public function getBy(string $column, string|int $value, array $attributes = []):mixed
    {
        return $this->model
        ->when(!empty($attributes[0]), function($q) use($attributes){
            $q->with($attributes);
        })
        ->where([$column => $value])->firstOrFail();
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes):Model
    {
        $model = $this->model;
        $model->name = $attributes['model_name'];
        $model->brand_id = $attributes['brand_id'];
        $model->seat_capacity = $attributes['seat_capacity'];
        $model->maximum_weight = $attributes['maximum_weight'];
        $model->hatch_bag_capacity = $attributes['hatch_bag_capacity'];
        $model->engine = $attributes['engine'];
        $model->description = $attributes['short_desc'];
        $model->image = fileUploader('vehicle/model/', 'png',$attributes['model_image']);;
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

            $model->name = $attributes['model_name'];
            $model->description = $attributes['short_desc'];
            $model->brand_id = $attributes['brand_id'];
            $model->seat_capacity = $attributes['seat_capacity'];
            $model->maximum_weight = $attributes['maximum_weight'];
            $model->hatch_bag_capacity = $attributes['hatch_bag_capacity'];
            $model->engine = $attributes['engine'];
            if(array_key_exists('model_image', $attributes)){
                $model->image = fileUploader('vehicle/model/', 'png',$attributes['model_image'], $model->image);
            }

        }else{
            $model->is_active = $attributes['status'];
        }

        $model->save();

        return $model;
    }

    /**
     * Remove the specified resource from storage.
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
     public function getByComparison(array $attributes):mixed
     {
        return $this->model->where('maximum_weight','>=', $attributes['weight'])->ofStatus()->get();
     }



    /**
     * @param array $attributes
     * @return mixed
     */
    public function trashed(array $attributes)
    {
        $search = $attributes['search'] ?? null;
        return $this->model->query()
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
        return $this->model->query()->onlyTrashed()->find($id)->restore();
    }

    public function permanentDelete(string $id): Model
    {
        $model = $this->model->query()->onlyTrashed()->find($id);
        $model->forceDelete();
        return $model;
    }


}

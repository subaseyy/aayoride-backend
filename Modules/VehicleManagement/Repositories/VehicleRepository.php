<?php

namespace Modules\VehicleManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\VehicleManagement\Entities\Vehicle;
use Modules\VehicleManagement\Interfaces\VehicleInterface;

class VehicleRepository implements VehicleInterface
{

    private $vehicle;

    public function __construct(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;

    }

    /**
     * Display a listing of the resource.
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

        $search = array_key_exists('search', $attributes) ? $attributes['search'] : '';
        $value = array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column = array_key_exists('query', $attributes) ? $attributes['query'] : '';

        $queryParam = ['search' => $search, 'query' => $column, 'value' => $value];

        $query = $this->vehicle
            ->query()
            ->when(!empty($relations[0]), function ($query) use ($relations) {
                $query->with($relations);
            })
            ->when($search, function ($query) use ($attributes) {

                $keys = explode(' ', $attributes['search']);
                $query->whereHas('brand', function ($query) use ($keys) {
                    return $query->where(function ($query) use ($keys) {
                        foreach ($keys as $key) {
                            $query->where('name', 'LIKE', '%' . $key . '%');
                        }
                    });
                })->orWhereHas('model', function ($query) use ($keys) {

                    return $query->where(function ($query) use ($keys) {
                        foreach ($keys as $key) {
                            $query->where('name', 'LIKE', '%' . $key . '%')
                                ->orWhere('seat_capacity', 'LIKE', '%' . $key . '%')
                                ->orWhere('hatch_bag_capacity', 'LIKE', '%' . $key . '%')
                                ->orWhere('engine', 'LIKE', '%' . $key . '%');
                        }
                    });
                })
                    ->orWhere(function ($query) use ($keys) {

                        return $query->where(function ($query) use ($keys) {
                            foreach ($keys as $key) {
                                $query->where('id', 'LIKE', '%' . $key . '%')
                                    ->orWhere('vin_number', 'LIKE', '%' . $key . '%')
                                    ->orWhere('licence_plate_number', 'LIKE', '%' . $key . '%')
                                    ->orWhere('fuel_type', 'LIKE', '%' . $key . '%');
                            }
                        });
                    });
            })
            ->when($column && $value != 'all', function ($query) use ($column, $value) {

                return $query->where($column, ($value == 'active' ? 1 : ($value == 'inactive' ? 0 : $value)));
            })
            ->when(!empty($except[0]), function ($query) use ($except) {
                $query->whereNotIn('id', $except);
            });

        if (!$dynamic_page) {
            return $query->latest()->paginate($limit)->appends($queryParam);
        } else {
            return $query->latest()->paginate($limit, ['*'], $offset);
        }

    }

    /**
     * Display a listing of the resource.
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return mixed
     */

    public function getBy(string $column, string|int $value, array $attributes = []): mixed
    {
        return $this->vehicle
            ->when(!empty($attributes[0]), function ($q) use ($attributes) {
                $q->with($attributes);
            })
            ->where([$column => $value])->firstOrFail();

    }

    /**
     * Store a newly created resource in storage.
     * @param array $attributes
     * @return Model
     */

    public function store(array $attributes): Model
    {
        $model = $this->vehicle;
        $model->brand_id = $attributes['brand_id'];
        $model->model_id = $attributes['model_id'];
        $model->category_id = $attributes['category_id'];
        $model->licence_plate_number = $attributes['licence_plate_number'];
        $model->licence_expire_date = $attributes['licence_expire_date'];
        $model->vin_number = $attributes['vin_number'];
        $model->transmission = $attributes['transmission'];
        $model->fuel_type = $attributes['fuel_type'];
        $model->ownership = $attributes['ownership'];
        $model->driver_id = $attributes['driver_id'];

        $documents = [];

        if (array_key_exists('upload_documents', $attributes)) {

            foreach ($attributes['upload_documents'] as $doc) {
                $extension = $doc->getClientOriginalExtension();
                $documents[] = fileUploader('vehicle/document/', $extension, $doc);
            }
            $model->documents = $documents;
        }


        $model->save();

        return $model;
    }

    /**
     * Update the specified resource in storage.
     * @param array $attributes
     * @param string $id
     * @return Model
     */

    public function update(array $attributes, string $id): Model
    {

        $model = $this->getBy(column: 'id', value: $id);

        if (array_key_exists('status', $attributes) ?? null) {
            $model->is_active = $attributes['status'];
            $model->save();

            return $model;
        }

        $model->brand_id = $attributes['brand_id'];
        $model->model_id = $attributes['model_id'];
        $model->category_id = $attributes['category_id'];
        $model->licence_plate_number = $attributes['licence_plate_number'];
        $model->licence_expire_date = $attributes['licence_expire_date'];
        $model->vin_number = $attributes['vin_number'];
        $model->transmission = $attributes['transmission'];
        $model->fuel_type = $attributes['fuel_type'];
        $model->ownership = $attributes['ownership'];
        $model->driver_id = $attributes['driver_id'];

        $documents = [];

        if ($attributes['upload_documents'] ?? null) {

            foreach ($attributes['upload_documents'] as $doc) {
                $extension = $doc->getClientOriginalExtension();
                $documents[] = fileUploader('vehicle/document/', $extension, $doc, $model->documents);
            }
            $model->documents = $documents;
        }


        $model->save();

        return $model;
    }

    /**
     * Remove the specified resource from storage.
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
     * @param array $attributes
     * @return mixed
     */
    public function trashed(array $attributes)
    {
        $search = $attributes['search'] ?? null;
        return $this->vehicle->query()
            ->when($search, function ($query) use ($search) {

                $keys = explode(' ', $search);
                $query->whereHas('brand', function ($query) use ($keys) {
                    return $query->where(function ($query) use ($keys) {
                        foreach ($keys as $key) {
                            $query->where('name', 'LIKE', '%' . $key . '%');
                        }
                    });
                })
                    ->orWhereHas('model', function ($query) use ($keys) {

                        return $query->where(function ($query) use ($keys) {
                            foreach ($keys as $key) {
                                $query->where('name', 'LIKE', '%' . $key . '%')
                                    ->orWhere('seat_capacity', 'LIKE', '%' . $key . '%')
                                    ->orWhere('hatch_bag_capacity', 'LIKE', '%' . $key . '%')
                                    ->orWhere('engine', 'LIKE', '%' . $key . '%');
                            }
                        });
                    })
                    ->orWhere(function ($query) use ($keys) {

                        return $query->where(function ($query) use ($keys) {
                            foreach ($keys as $key) {
                                $query->where('id', 'LIKE', '%' . $key . '%')
                                    ->orWhere('vin_number', 'LIKE', '%' . $key . '%')
                                    ->orWhere('licence_plate_number', 'LIKE', '%' . $key . '%')
                                    ->orWhere('fuel_type', 'LIKE', '%' . $key . '%');
                            }
                        });
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
        return $this->vehicle->query()->onlyTrashed()->find($id)->restore();
    }

    public function permanentDelete(string $id): Model
    {
        $model = $this->vehicle->query()->onlyTrashed()->find($id);
        $model->forceDelete();
        return $model;
    }

}

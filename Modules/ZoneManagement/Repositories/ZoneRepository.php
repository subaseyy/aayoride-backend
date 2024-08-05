<?php

namespace Modules\ZoneManagement\Repositories;

use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use Modules\ZoneManagement\Entities\Zone;
use Modules\ZoneManagement\Interfaces\ZoneInterface;

class ZoneRepository implements ZoneInterface
{


    public function __construct(private Zone $zone)
    {
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

        $query = $this->zone
            ->query()
            ->when(!empty($relations[0]), function ($query) use ($relations) {
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
            ->when($column && $value != 'all', function ($query) use ($column, $value) {
                return $query->where($column, ($value == 'active' ? 1 : ($value == 'inactive' ? 0 : $value)));
            })
            ->when(!empty($except[0]), function ($query) use ($except) {
                $query->whereNotIn('id', $except);
            })
            ->when(array_key_exists('withCount', $attributes), function ($query) use ($attributes) {
                $query->withCount($attributes['withCount']);
            })
            ->latest();

        if (!$dynamic_page) {
            return $query->paginate($limit)->appends($queryParam);
        }

        return $query->paginate(perPage: $limit, page: $offset);
    }

    /**
     * Display the specified resource.
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return Model
     */

    public function getBy(string $column, string|int $value, array $attributes = []): mixed
    {
        return $this->zone
            ->selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")
            ->where([$column => $value])
            ->when(array_key_exists('relations', $attributes), function ($query) use ($attributes) {
                $query->with($attributes['relations']);
            })
            ->first();
    }

    /**
     * Store a newly created resource in storage.
     * @param array $attributes
     * @return Model
     */

    public function store(array $attributes): Model
    {
        $value = $attributes['coordinates'];

        foreach (explode('),(', trim($value, '()')) as $index => $single_array) {
            if ($index == 0) {
                $lastcord = explode(',', $single_array);
            }
            $coords = explode(',', $single_array);
            $polygon[] = new Point($coords[0], $coords[1]);
        }
        $polygon[] = new Point($lastcord[0], $lastcord[1]);

        $model = $this->zone;
        $model->name = $attributes['zone_name'];
        $model->coordinates = new Polygon([new LineString($polygon)]);
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

        if (!array_key_exists('status', $attributes)) {

            $value = $attributes['coordinates'];

            foreach (explode('),(', trim($value, '()')) as $index => $single_array) {
                if ($index == 0) {
                    $lastcord = explode(',', $single_array);
                }
                $coords = explode(',', $single_array);
                $polygon[] = new Point($coords[0], $coords[1]);
            }
            $polygon[] = new Point($lastcord[0], $lastcord[1]);


            $model->name = $attributes['zone_name'];
            $model->coordinates = new Polygon([new LineString($polygon)]);

        } else {

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

    public function destroy(string $id): Model
    {
        $model = $this->getBy(column: 'id', value: $id);
        $model->delete();
        return $model;
    }


    /**
     * @param $point
     * @return mixed
     */
    public function getByPoints($point): mixed
    {
        return $this->zone->whereContains('coordinates',$point)->first();
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function trashed(array $attributes = [])
    {
        $search = $attributes['search'] ?? null;
        $relations = $attributes['relations'] ?? null;
        return $this->zone->query()
            ->when($relations, function ($query) use ($relations) {
                $query->with($relations);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $keys = explode(' ', $search);
                    foreach ($keys as $key) {
                        $query->where('name', 'like', '%' . $key . '%');
                    }
                });
            })
            ->onlyTrashed()
            ->paginate(paginationLimit());

    }

    /**
     * @param string $id
     * @return mixed
     */

    public function restore(string $id)
    {
        return $this->zone->query()->onlyTrashed()->find($id)->restore();
    }

    public function permanentDelete(string $id): Model
    {
        $model = $this->zone->query()->onlyTrashed()->find($id);
        $model->forceDelete();
        return $model;
    }

    public function storeWithException($attributes)
    {
        $value = $attributes['coordinates'];

        foreach (explode('),(', trim($value, '()')) as $index => $single_array) {
            if ($index == 0) {
                $lastcord = explode(',', $single_array);
            }
            $coords = explode(',', $single_array);
            $polygon[] = new Point($coords[0], $coords[1]);
        }
        $polygon[] = new Point($lastcord[0], $lastcord[1]);

        $model = $this->zone;
        $model->name = $attributes['zone_name'];
        $model->coordinates = new Polygon([new LineString($polygon)]);
        $model->save();

        return $model;
    }
}

<?php

namespace Modules\PromotionManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\PromotionManagement\Entities\BannerSetup;
use Modules\PromotionManagement\Interfaces\BannerInterface;

class BannerRepository implements BannerInterface
{


    public function __construct(private BannerSetup $banner)
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

        $query = $this->banner
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
            });

        if (!$dynamic_page) {
            return $query->paginate(paginationLimit())->appends($queryParam);
        }

        return $query->latest()->paginate($limit, ['*'], 'page', $offset);
    }

    /**
     * Display a listing of the resource.
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return Model
     */

    public function getBy(string $column, string|int $value, array $attributes = []): Model
    {
        return $this->banner->where([$column => $value])->firstOrFail();
    }

    /**
     * Store a newly created resource in storage.
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {
        $banner = $this->banner;
        $banner->name = $attributes['banner_title'];
        $banner->description = $attributes['short_desc'];
        $banner->time_period = $attributes['time_period'];
        $banner->display_position = $attributes['display_position'] ?? null;
        $banner->redirect_link = $attributes['redirect_link'];
        $banner->banner_group = $attributes['banner_group'] ?? null;
        $banner->start_date = $attributes['start_date'] ?? null;
        $banner->end_date = $attributes['end_date'] ?? null;
        $banner->image = fileUploader('promotion/banner/', 'png', $attributes['banner_image']);
        $banner->save();
        return $banner;
    }

    /**
     * Update the specified resource in storage.
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model
    {
        $banner = $this->banner->firstWhere('id', $id);

        if (array_key_exists('status', $attributes) ?? null) {
            $banner->is_active = $attributes['status'];
            $banner->save();

            return $banner;
        }

        $banner->name = $attributes['banner_title'];
        $banner->description = $attributes['short_desc'];
        $banner->time_period = $attributes['time_period'];
        $banner->redirect_link = $attributes['redirect_link'];
        $banner->start_date = $attributes['start_date'] ?? null;
        $banner->end_date = $attributes['end_date'] ?? null;

        if (array_key_exists('banner_image', $attributes)) {
            $banner->image = fileUploader('promotion/banner/', 'png', $attributes['banner_image'], $banner->image);

        }
        $banner->save();

        return $banner;
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return Model
     */

    public function destroy(string $id): Model
    {
        $banner = $this->getBy(column: 'id', value: $id);
        $banner->delete();
        return $banner;
    }


    /**
     * @param array $attributes
     * @return mixed
     */
    public function trashed(array $attributes)
    {
        $search = $attributes['search'] ?? null;
        return $this->banner->query()
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
        return $this->banner->query()->onlyTrashed()->find($id)->restore();
    }

    public function permanentDelete(string $id): Model
    {
        $model = $this->banner->query()->onlyTrashed()->find($id);
        $model->forceDelete();
        return $model;
    }

}

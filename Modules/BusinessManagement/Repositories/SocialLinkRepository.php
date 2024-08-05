<?php

namespace Modules\BusinessManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\BusinessManagement\Entities\SocialLink;
use Modules\BusinessManagement\Interfaces\SocialLinkInterface;

class SocialLinkRepository implements SocialLinkInterface
{
    public function __construct(
        private SocialLink $socialLink
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
    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes= [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $search = array_key_exists('search', $attributes)? $attributes['search'] : '';
        $value =  array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column =  array_key_exists('query', $attributes) ? $attributes['query'] : '';

        $queryParam = ['search' => $search, 'query' => $column, 'value' => $value];

        $query = $this->socialLink
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
        }

        return $query->latest()->paginate($limit, ['*'], $offset);

    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return mixed
     */
    public function getBy(string $column, string|int $value, array $attributes = []): mixed
    {
        return $this->socialLink->query()->where([$column => $value])->firstOrFail();
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {
        $socialLink = $this->socialLink;
        $socialLink->name = $attributes['name'];
        $socialLink->link = $attributes['link'];
        $socialLink->save();

        return $socialLink;
    }

    public function update(array $attributes, string $id): Model
    {
        $socialLink = $this->getBy(column: 'id', value: $id);
        if (!array_key_exists('status', $attributes)) {
            $socialLink->name = $attributes['name'];
            $socialLink->link = $attributes['link'];
        } else {
            $socialLink->is_active = $attributes['status'];

        }
        $socialLink->save();

        return $socialLink;
    }

    public function destroy(string $id): Model
    {
        $link = $this->getBy(column: 'id', value: $id);
        $link->delete();
        return $link;
    }


}

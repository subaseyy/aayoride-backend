<?php

namespace Modules\BusinessManagement\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\BusinessManagement\Entities\NotificationSetting;
use Modules\BusinessManagement\Interfaces\NotificationSettingInterface;

class NotificationSettingRepository implements NotificationSettingInterface
{

    public function __construct(
        private NotificationSetting $notification
    )
    {}

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes= [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $value =  array_key_exists('value', $attributes)? $attributes['value'] : 'all';
        $column = array_key_exists('query', $attributes)? $attributes['query'] : '';
        $search = array_key_exists('search', $attributes)? $attributes['search'] : '';
        $queryParams = ['value' => $value, 'search' => $search, 'column' => $column];

        $query = $this->notification
            ->query()
            ->when($search, function ($query) use($search){
                $query->where(function ($query)use($search){
                    $keys = explode(' ', $search);
                    foreach ($keys as $key) {
                        $query->where('name', 'like', '%'. $key. '%');
                    }
                });
            })
            ->latest();

        if ($dynamic_page) {
            return $query->paginate($limit, ['*'], $offset);
        }

        return $query->paginate(paginationLimit())
            ->appends($queryParams);
    }

    public function getBy(string $column, string|int $value, array $attributes = []): ?Model
    {
        return $this->notification
            ->query()
            ->where($column, $value)
            ->firstOrFail();
    }

    public function store(array $attributes): Model
    {
        // TODO: Implement store() method.
    }

    public function update(array $attributes, string $id): Model
    {
        $notification = $this->getBy('id', $id);
        if ($attributes['type'] == 'email')  {
            $notification->email = $attributes['status'];
        }
        elseif ($attributes['type'] == 'push')  {
            $notification->push = $attributes['status'];
        }
        $notification->save();

        return $notification;
    }
    public function destroy(string $id): Model
    {
        // TODO: Implement destroy() method.
    }

}

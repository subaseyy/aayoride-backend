<?php

namespace Modules\BusinessManagement\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\BusinessManagement\Entities\FirebasePushNotification;
use Modules\BusinessManagement\Interfaces\FirebasePushNotificationInterface;

class FirebasePushNotificationRepository implements FirebasePushNotificationInterface
{
    public function __construct(
        private FirebasePushNotification $notification
    )
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes= [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $notifications =  $this->notification->query()
            ->when($attributes['status'] ?? null, fn($query)=> $query->where('status', $attributes['status']));

        if ($dynamic_page) {
            return $notifications->paginate($limit, ['*'], $offset);
        }

        return $notifications->paginate($limit);
    }

    public function getBy(string $column, string|int $value, array $attributes = []): ?Model
    {
        // TODO: Implement findById() method.
    }

    public function store(array $attributes): Model
    {
        return $this->notification->query()
            ->updateOrCreate(
                ['name' => $attributes['name']], [
                    'name' => $attributes['name'],
                    'value' => $attributes['value'],
                    'status' => 1
                ]);

    }

    public function update(array $attributes, string $id): Model
    {
        // TODO: Implement update() method.
    }

    public function destroy(string $id): Model
    {
        // TODO: Implement destroy() method.
    }

    public function getAllCollection() : Collection
    {
        return $this->notification
            ->query()
            ->get();
    }

}

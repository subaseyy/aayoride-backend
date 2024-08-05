<?php

namespace Modules\AdminModule\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\AdminModule\Entities\AdminNotification;
use Modules\AdminModule\Interfaces\AdminNotificationInterface;

class AdminNotificationRepository implements AdminNotificationInterface
{
    public function __construct(
        private AdminNotification $notification
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
    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        return $this->notification
            ->query()
            ->ofNotSeen()
            ->latest()
            ->get();
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return mixed|Model
     */
    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        // TODO: Implement getBy() method.
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {
        // TODO: Implement store() method.
    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model
    {
        if ($id == 0) {
            $notification = $this->notification->query()->update(['is_seen' => true]);
        } else {
            $notification = $this->notification->query()->firstWhere('id', $id);
            $notification->is_seen = true;
            $notification->save();
        }
        return $notification;
    }

    /**
     * @param string $id
     * @return Model
     */
    public function destroy(string $id): Model
    {
        // TODO: Implement destroy() method.
    }
}

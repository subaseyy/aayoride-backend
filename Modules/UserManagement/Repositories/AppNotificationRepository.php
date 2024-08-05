<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Entities\AppNotification;
use Modules\UserManagement\Entities\UserAddress;
use Modules\UserManagement\Interfaces\AppNotificationInterface;

class AppNotificationRepository implements AppNotificationInterface
{

    public function __construct(
        private UserAddress $address,
        private AppNotification $notification)
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $queryParam = array_key_exists('query', $attributes) ? $attributes['query'] : null;
        $query = $this->notification
            ->query()
            ->when(array_key_exists('column', $attributes), fn($query) => $query->where($attributes['column'], $attributes['value']))
            ->latest();
        if (!$dynamic_page) {
            return $query
                ->paginate($limit)
                ->appends($queryParam);
        }
        return $query->paginate(perPage: $limit, page: $offset);
    }

    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        return $this->address->query()->where($column, $value)
        ->when(array_key_exists('column', $attributes) && array_key_exists('value', $attributes), function($query) use($attributes){
            $query->whereIn($attributes['column'], $attributes['value']);
        })
        ->first();
    }

    public function store(array $attributes): Model
    {
        $notification = $this->notification;
        $notification->user_id = $attributes['user_id'];
        $notification->ride_request_id = $attributes['ride_request_id'];
        $notification->title = $attributes['title']?? 'Title Not Found';
        $notification->description = $attributes['description']?? 'Description Not Found';
        $notification->type = $attributes['type'];
        $notification->action = $attributes['action'];
        $notification->save();

        return $this->notification;
    }

    public function update(array $attributes, string $id): Model
    {
        $address = $this->getBy(column: 'id', value: $id);
        $address->user_id = $attributes['user_id'];
        $address->latitude = $attributes['latitude'];
        $address->longitude = $attributes['longitude'];
        $address->city = $attributes['city']??null;
        $address->street = $attributes['street']??null;
        $address->zip_code = $attributes['zip_code']??null;
        $address->country = $attributes['country']??null;
        $address->address = $attributes['address'];
        $address->address_label = $attributes['address_label'];
        $address->save();

        return $address;

    }

    public function destroy(string $id): Model
    {
        $customer = $this->getBy(column: 'id', value: $id);
        $customer->delete();
        return $customer;

    }
}

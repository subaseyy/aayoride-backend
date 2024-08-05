<?php

namespace Modules\UserManagement\Repositories;

use Modules\UserManagement\Entities\UserLastLocation;
use Modules\UserManagement\Interfaces\UserLastLocationInterface;
use function React\Promise\all;

class UserLastLocationRepository implements UserLastLocationInterface
{
    public function __construct(private UserLastLocation $last_location)
    {
    }

    public function get(int $limit, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): mixed
    {
        return $this->last_location->selectRaw("* ,
        ( 6371 * acos( cos( radians(?) ) *
          cos( radians( latitude ) )
          * cos( radians( longitude ) - radians(?)
          ) + sin( radians(?) ) *
          sin( radians( latitude ) ) )
        ) AS distance", [$attributes['latitude'], $attributes['longitude'], $attributes['latitude']])
            ->where('type', '=', 'driver')
            ->having("distance", "<", $attributes['radius'])
            ->orderBy("distance")
            ->limit($limit)
            ->get();
    }

    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        return $this->last_location
            ->query()
            ->where($column, $value)
            ->first();
    }

    public function updateOrCreate($attributes): mixed
    {
        $location = $this->last_location->query()
            ->updateOrInsert(['user_id' => $attributes['user_id']], [
                'type' => $attributes['type'],
                'latitude' => $attributes['latitude'],
                'longitude' => $attributes['longitude'],
                'zone_id' => $attributes['zone_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        return $location;
    }

    public function getNearestDrivers($attributes): mixed
    {
        return $this->last_location
            ->selectRaw("* ,( 6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude)
            - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                [$attributes['latitude'], $attributes['longitude'], $attributes['latitude']])
            ->where('type', 'driver')
            ->where('zone_id', $attributes['zone_id'])
            ->having('distance', '<', $attributes['radius'])
            ->with(['user.vehicle.category', 'driverDetails', 'user'])
            ->whereHas('user', fn($query) => $query->where('is_active', true))
            ->whereHas('driverDetails', fn($query) => $query->where('is_online', true)
                ->whereNotIn('availability_status', ['unavailable', 'on_trip'])
            )
            ->whereHas('user.vehicle', fn($query) => $query->where('is_active', true))
            ->when(array_key_exists('vehicle_category_id', $attributes), function ($query) use ($attributes) {
                $query->whereHas('user.vehicle', fn($query) => $query->ofStatus(1)->where('category_id', $attributes['vehicle_category_id']));
            })
            ->orderBy('distance')
            ->get();
    }
}

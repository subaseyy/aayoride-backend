<?php

namespace Modules\FareManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\FareManagement\Entities\TripFare;
use Modules\FareManagement\Entities\ZoneWiseDefaultTripFare;
use Modules\FareManagement\Interfaces\TripFareInterface;

class TripFareRepository implements TripFareInterface
{
    public function __construct(
        private TripFare $tripFare
    ) {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $query = $this->tripFare->query()
            ->when(array_key_exists('query', $attributes), function ($query) use ($attributes) {
                $query->where($attributes['query'], $attributes['value']);
            })
            ->when(!empty($relations[0]), function ($query) use ($relations) {
                $query->with($relations);
            });
        if ($dynamic_page) {
            return $query->paginate(perPage: $limit, page: $offset);
        }
        return $query->paginate($limit);
    }

    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {

        return $this->tripFare->query()
            ->where([
                $column => $value,
                'zone_id' => $attributes['zone_id']
            ])
            ->first();
    }

    public function store(array $attributes): Model
    {
        $last_query = [];
        if ($attributes['default_fare_id']) {
            $defaultTripFare = ZoneWiseDefaultTripFare::find($attributes['default_fare_id']);
        } else {
            $defaultTripFare = new ZoneWiseDefaultTripFare();
        }

        $defaultTripFare->zone_id = $attributes['zone_id'];
        $defaultTripFare->base_fare = $attributes['base_fare'] ?? 0;
        $defaultTripFare->base_fare_per_km = $attributes['base_fare_per_km'] ?? 0;
        $defaultTripFare->waiting_fee_per_min = $attributes['waiting_fee'] ?? 0;
        $defaultTripFare->cancellation_fee_percent = $attributes['cancellation_fee'] ?? 0;
        $defaultTripFare->min_cancellation_fee = $attributes['min_cancellation_fee'] ?? 0;
        $defaultTripFare->idle_fee_per_min = $attributes['idle_fee'] ?? 0;
        $defaultTripFare->trip_delay_fee_per_min = $attributes['trip_delay_fee'] ?? 0;
        $defaultTripFare->penalty_fee_for_cancel = 0;
        $defaultTripFare->fee_add_to_next = 0;
        $defaultTripFare->category_wise_different_fare = $attributes['category_wise_different_fare'];
        $defaultTripFare->minimum_pickup_distance = $attributes['minimum_pickup_distance'];
        $defaultTripFare->pickup_bonus_amount = $attributes['pickup_bonus_amount'];
        $defaultTripFare->save();

        foreach (($attributes['vehicleCategories']) as $vehicleCategories) {
            $search['zone_id'] = $attributes['zone_id'];

            $tripFare = $this->tripFare->query()
                ->where([
                    'vehicle_category_id' => $vehicleCategories->id,
                    'zone_id' => $attributes['zone_id']
                ])
                ->first();

            if (!empty($tripFare)) {
                $tripFare->delete();
            }

            if (array_key_exists('vehicle_category_' . $vehicleCategories->id, $attributes)) {
                $tripFare = new TripFare();

                $tripFare->vehicle_category_id = $attributes['vehicle_category_' . $vehicleCategories->id];
                $tripFare->zone_wise_default_trip_fare_id = $defaultTripFare->id;
                $tripFare->zone_id = $attributes['zone_id'];
                if ($attributes['category_wise_different_fare'] == 0) {
                    $tripFare->base_fare = $attributes['base_fare'] ?? 0;
                    $tripFare->base_fare_per_km = $attributes['base_fare_per_km'] ?? 0;
                    $tripFare->waiting_fee_per_min = $attributes['waiting_fee'] ?? 0;
                    $tripFare->cancellation_fee_percent = $attributes['cancellation_fee'] ?? 0;
                    $tripFare->min_cancellation_fee = $attributes['min_cancellation_fee'] ?? 0;
                    $tripFare->idle_fee_per_min = $attributes['idle_fee'] ?? 0;
                    $tripFare->trip_delay_fee_per_min = $attributes['trip_delay_fee'] ?? 0;
                    $tripFare->penalty_fee_for_cancel = $attributes['trip_delay_fee'] ?? 0;
                    $tripFare->fee_add_to_next = $attributes['trip_delay_fee'] ?? 0;
                    $tripFare->minimum_pickup_distance = $attributes['minimum_pickup_distance'] ?? 0;
                    $tripFare->pickup_bonus_amount = $attributes['pickup_bonus_amount'] ?? 0;
                } else {
                    $tripFare->base_fare = $attributes['base_fare_' . $vehicleCategories->id] ?? 0;
                    $tripFare->base_fare_per_km = $attributes['base_fare_per_km_' . $vehicleCategories->id] ?? 0;
                    $tripFare->waiting_fee_per_min = $attributes['waiting_fee_' . $vehicleCategories->id] ?? 0;
                    $tripFare->cancellation_fee_percent = $attributes['cancellation_fee_' . $vehicleCategories->id] ?? 0;
                    $tripFare->min_cancellation_fee = $attributes['min_cancellation_fee_' . $vehicleCategories->id] ?? 0;
                    $tripFare->idle_fee_per_min = $attributes['idle_fee_' . $vehicleCategories->id] ?? 0;
                    $tripFare->trip_delay_fee_per_min = $attributes['trip_delay_fee_' . $vehicleCategories->id] ?? 0;
                    $tripFare->penalty_fee_for_cancel = $attributes['penalty_fee_for_cancel_' . $vehicleCategories->id] ?? 0;
                    $tripFare->fee_add_to_next = $attributes['fee_add_to_next_' . $vehicleCategories->id] ?? 0;
                    $tripFare->minimum_pickup_distance = $attributes['minimum_pickup_distance_' . $vehicleCategories->id]??0;
                    $tripFare->pickup_bonus_amount = $attributes['pickup_bonus_amount_'. $vehicleCategories->id] ?? 0;
                }
                $tripFare->save();
                $last_query = $tripFare;
            }
        }
        return $last_query;
    }

    public function update(array $attributes, string $id): Model
    {
        // TODO: Implement update() method.
    }

    public function destroy(string $id): Model
    {
        //
    }

    public function delete($id)
    {
        return  $this->tripFare->query()->where('vehicle_category_id', $id)->delete();
    }
}

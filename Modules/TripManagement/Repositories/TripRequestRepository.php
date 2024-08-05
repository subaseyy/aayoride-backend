<?php

namespace Modules\TripManagement\Repositories;

use Carbon\Carbon;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\TripManagement\Entities\TripRequest;
use Modules\TripManagement\Interfaces\TripRequestInterfaces;

class TripRequestRepository implements TripRequestInterfaces
{

    public function __construct(
        private TripRequest $trip
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
        $search = $attributes['search'] ?? null;
        $extraColumn = $attributes['column_name'] ?? null;
        $extraColumnValue = $attributes['column_value'] ?? null;
        $queryParams = ['search' => $search];

        $query = $this->trip
            ->query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $keys = explode(' ', $search);
                    foreach ($keys as $key) {
                        $query->where('ref_id', 'like', '%' . $key . '%');
                    }
                });
            })
            ->when(($attributes['relations'] ?? null), fn($query) => $query->with($attributes['relations']))
            ->when(!empty($relations), fn($query) => $query->with($relations))
            ->when($attributes['from'] ?? null,
                fn($query) => $query->whereBetween('created_at', [$attributes['from'], $attributes['to']]))
            ->when($attributes['column'] ?? null,
                fn($query) => $query->where($attributes['column'], $attributes['value']))
            ->when($extraColumn && $extraColumnValue,
                fn($query) => $query->whereIn($extraColumn, $extraColumnValue))
            ->when(($attributes['whereNotInColumn'] ?? null),
                fn($query) => $query->whereNotIn($attributes['whereNotInColumn'], $attributes['whereNotInValue']))
            ->when(($attributes['withAvgRelation'] ?? null),
                fn($query) => $query->withAvg($attributes['withAvgRelation'], $attributes['withAvgColumn']))
            ->when(($attributes['type'] ?? null), fn($query) => $query->type($attributes['type']))
            ->latest();

        if ($dynamic_page) {
            return $query->paginate(perPage: $limit, page: $offset);
        }
        return $query->paginate($limit)
            ->appends($queryParams);
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return mixed|Model
     */
    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        $extraColumn = $attributes['column_name'] ?? null;
        $extraColumnValue = $attributes['column_value'] ?? null;

        return $this->trip
            ->query()
            ->when(($attributes['relations'] ?? null), fn($query) => $query->with($attributes['relations']))
            ->when(($attributes['fare_biddings'] ?? null),
                fn($query) => $query->with(['fare_biddings' => fn($query) => $query->where('driver_id', $attributes['fare_biddings'])]))
            ->when($column && $value, fn($query) => $query->where($column, $value))
            ->when($extraColumn, fn($query) => $query->where($extraColumn, $extraColumnValue))
            ->when($attributes['latest'] ?? null, fn($query) => $query->latest())
            ->when(($attributes['whereNotInColumn'] ?? null),
                fn($query) => $query->whereNotIn($attributes['whereNotInColumn'], $attributes['whereNotInValue']))
            ->when(($attributes['withAvgRelation'] ?? null),
                fn($query) => $query->withAvg($attributes['withAvgRelation'], $attributes['withAvgColumn']))
            ->when(($attributes['withTrashed'] ?? null), fn($query) => $query->withTrashed())
            ->latest()
            ->first();
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {
        try {

            DB::beginTransaction();

            $trip = $this->trip;
            $trip->customer_id = $attributes['customer_id'] ?? null;
            $trip->vehicle_category_id = $attributes['vehicle_category_id'] ?? null;
            $trip->zone_id = $attributes['zone_id'] ?? null;
            $trip->area_id = $attributes['area_id'] ?? null;
            $trip->actual_fare = $attributes['actual_fare'];
            $trip->estimated_fare = $attributes['estimated_fare'] ?? 0;
            $trip->rise_request_count = $attributes['rise_request_count'] ?? 0;
            $trip->estimated_distance = str_replace(',', '', $attributes['estimated_distance']) ?? null;
            $trip->payment_method = $attributes['payment_method'] ?? null;
            $trip->note = $attributes['note'] ?? null;
            $trip->type = $attributes['type'];
            $trip->entrance = $attributes['entrance'] ?? null;
            $trip->encoded_polyline = $attributes['encoded_polyline'] ?? null;
            $trip->save();

            $trip->tripStatus()->create([
                'customer_id' => $attributes['customer_id'],
                'pending' => now()
            ]);

            $coordinates = [
                'pickup_coordinates' => $attributes['pickup_coordinates'],
                'start_coordinates' => $attributes['pickup_coordinates'],
                'destination_coordinates' => $attributes['destination_coordinates'],
                'pickup_address' => $attributes['pickup_address'],
                'destination_address' => $attributes['destination_address'],
                'customer_request_coordinates' => $attributes['customer_request_coordinates']
            ];
            $int_coordinates = json_decode($attributes['intermediate_coordinates']);
            if (!is_null($int_coordinates)) {
                foreach ($int_coordinates as $key => $ic) {
                    if ($key == 0) {
                        $coordinates['int_coordinate_1'] = new Point($ic[0], $ic[1]);
                    } elseif ($key == 1) {
                        $coordinates['int_coordinate_2'] = new Point($ic[0], $ic[1]);
                    }
                }

            }
            $coordinates['intermediate_coordinates'] = $attributes['intermediate_coordinates'] ?? null;
            $coordinates['intermediate_addresses'] = $attributes['intermediate_addresses'] ?? null;

            $trip->coordinate()->create($coordinates);
            $trip->fee()->create();
            $delay_time =
            $trip->time()->create([
                'estimated_time' => str_replace(',', '', $attributes['estimated_time'])
            ]);

            if ($attributes['type'] == 'parcel') {
                $trip->parcel()->create([
                    'payer' => $attributes['payer'],
                    'weight' => $attributes['weight'],
                    'parcel_category_id' => $attributes['parcel_category_id'],
                ]);

                $sender = [
                    'name' => $attributes['sender_name'],
                    'contact_number' => $attributes['sender_phone'],
                    'address' => $attributes['sender_address'],
                    'user_type' => 'sender'
                ];
                $receiver = [
                    'name' => $attributes['receiver_name'],
                    'contact_number' => $attributes['receiver_phone'],
                    'address' => $attributes['receiver_address'],
                    'user_type' => 'receiver'
                ];
                $trip->parcelUserInfo()->createMany([$sender, $receiver]);

            }

            DB::commit();

        } catch (\Exception $e) {
            //throw $th;
            DB::rollback();
            abort(403, message: $e->getMessage());
        }

        return $this->trip;

    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model
    {
        $trip = $this->trip->firstWhere($attributes['column'], $id);

        $trip_request_keys = ['customer_id', 'driver_id', 'vehicle_category_id', 'vehicle_id', 'zone_id', 'estimated_fare', 'actual_fare',
            'estimated_distance', 'paid_fare', 'actual_distance', 'accepted_by', 'payment_method', 'payment_status',  'coupon_id',
            'coupon_amount', 'vat_tax', 'additional_charge', 'trx_id', 'note', 'otp', 'rise_request_count', 'type', 'current_status', 'tips',
            'is_paused', 'map_screenshot'
            ];

        DB::transaction(function () use ($trip_request_keys, $attributes, $trip) {
            foreach ($trip_request_keys as $key) {
                if ($attributes['rise_request_count'] ?? null) {
                    $trip->increment('rise_request_count');
                } else {
                    ($attributes[$key] ?? null) ? $trip->$key = $attributes[$key] : null;
                }
            }
            ($attributes['map_screenshot'] ?? null) ? $trip->map_screenshot = fileUploader('trip/screenshots/', 'png', $attributes['map_screenshot'], $trip->map_screenshot ) : null;

            $trip->save();
            if ($attributes['tripStatus'] ?? null) {
                $trip->tripStatus()->update([$attributes['current_status'] => now()]);
            }

            if ($attributes['driver_arrival_time'] ?? null) {
                $trip->time()->update(['driver_arrival_time' => $attributes['driver_arrival_time']]);
            }
            if ($attributes['coordinate'] ?? null) {
                $trip->coordinate()->update($attributes['coordinate']);
            }
        });

        return $trip;
    }

    /**
     * @param string $id
     * @return Model
     */
    public function destroy(string $id): Model
    {
        $trip = $this->trip->query()->find($id);
        $trip->delete();

        return $trip;
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function updateRelationalTable($attributes): mixed
    {
        $trip = $this->getBy(column: $attributes['column'], value: $attributes['value']);
        if ($attributes['trip_status'] ?? null) {
            $trip->current_status = $attributes['trip_status'];
            $trip->save();
            $trip->tripStatus()->update([
                $attributes['trip_status'] => now()
            ]);
        }
        if ($attributes['trip_cancellation_reason'] ?? null){
            $trip->trip_cancellation_reason = $attributes['trip_cancellation_reason'];
            $trip->save();
        }

        if ($attributes['driver_id'] ?? null) {
            $trip->driver_id = null;
            $trip->save();
        }

        if ($attributes['coordinate'] ?? null) {
            $coordinate = $trip->coordinate;
            if ($coordinate) {
                $coordinate->update([
                    'drop_coordinates'=>$attributes['coordinate']['drop_coordinates'],
                ]);
                $coordinate->save();
            }
        }
        if ($attributes['fee'] ?? null) {
            $trip->fee()->update($attributes['fee']);
        }
        return $trip->tripStatus;
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function getPendingRides($attributes): mixed
    {
        return $this->trip->query()
            ->when($attributes['relations'] ?? null, fn($query) => $query->with($attributes['relations']))
            ->with([
                'fare_biddings' => fn($query) => $query->where('driver_id', auth()->id()),
//                'coordinate' => fn($query) => $query->whereRaw("ST_Distance_Sphere($column, POINT($location->longitude, $location->latitude)) < $distance")
                'coordinate' => fn($query) => $query->distanceSphere('pickup_coordinates', $attributes['driver_locations'], $attributes['distance'])
            ])
            ->whereHas('coordinate',
                fn($query) => $query->distanceSphere('pickup_coordinates', $attributes['driver_locations'], $attributes['distance']))
            ->when($attributes['withAvgRelation'] ?? null,
                fn($query) => $query->withAvg($attributes['withAvgRelation'], $attributes['withAvgColumn']))
            ->whereDoesntHave('ignoredRequests', fn($query) => $query->where('user_id', auth()->id()))
            ->where(fn($query) =>
                $query->where('vehicle_category_id', $attributes['vehicle_category_id'])
                    ->orWhereNull('vehicle_category_id')
            )
            ->where(['zone_id' => $attributes['zone_id'], 'current_status' => PENDING,])
            ->orderBy('created_at', 'desc')
            ->paginate(perPage: $attributes['limit'], page: $attributes['offset']);
    }

    public function leaderBoard(array $attributes)
    {
        return $this->trip
            ->query()
            ->whereHas('driver',
                fn($query) => $query->where('deleted_at', null))
            ->whereHas('customer',
                fn($query) => $query->where('deleted_at', null))
            ->when(($attributes['relations'] ?? null), fn($query) => $query->with($attributes['relations']))
            ->when(($attributes['whereNotNull'] ?? null),
                fn($query) => $query->whereNotNull($attributes['whereNotNull']))
            ->when(($attributes['selectRaw'] ?? null), fn($query) => $query->selectRaw($attributes['selectRaw']))
            ->when(($attributes['groupBy'] ?? null), fn($query) => $query->groupBy($attributes['groupBy']))
            ->when(($attributes['orderBy'] ?? null),
                fn($query) => $query->orderBy($attributes['orderBy'], $attributes['direction'] ?? 'asc'))
            ->when(($attributes['start'] ?? null),
                fn($query) => $query->whereBetween('created_at', [$attributes['start'], $attributes['end']]))
            ->paginate(perPage: $attributes['limit'], page: $attributes['offset']);
    }

    public function getStat(array $attributes)
    {
        $query = $this->trip
            ->where($attributes['column'], $attributes['value'])
            ->when(($attributes['from'] ?? null), fn($query) => $query->whereBetween('created_at', [$attributes['from'], $attributes['to']]));

        if ($attributes['sum'] ?? null) {
            return $query->sum($attributes['sum']);
        }
        if ($attributes['count'] ?? null) {
            return $query->count($attributes['count']);
        }
    }

    public function getIncompleteRide(array $attributes = []): mixed
    {
        return $this->trip
            ->query()
            ->when(($attributes['relations'] ?? null), fn($query) => $query->with($attributes['relations']))
            ->where(fn($query) => $query->whereNotIn('current_status', ['completed', 'cancelled'])
                ->orWhere('payment_status', UNPAID)
            )
            ->when(($attributes['type'] ?? null), fn($query) => $query->where('type', $attributes['type']))
            ->where($attributes['column'], $attributes['value'])->first();
    }

    public function overviewStat (array $attributes)
    {
        return $this->trip->query()
            ->when($attributes['from'] ?? null, fn ($query) => $query->whereBetween('created_at', [$attributes['from'], $attributes['to']]))
            ->selectRaw('current_status, count(*) as total_records')
            ->groupBy('current_status')->get();
    }


    public function trashed(array $attributes)
    {
        $search = $attributes['search'] ?? null;
        return $this->trip->query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $keys = explode(' ', $search);
                    foreach ($keys as $key) {
                        $query->where('ref_id', 'like', '%' . $key . '%');
                    }
                });
            })
            ->onlyTrashed()
            ->paginate(paginationLimit());

    }

    public function restore(string $id)
    {
        return $this->trip->query()->onlyTrashed()->find($id)->restore();
    }



    public function pendingParcelList(array $attributes, string $type)
    {
        if ($type=="driver"){
            return $this->trip->query()
                ->with(['customer','driver','vehicleCategory','vehicleCategory.tripFares','vehicle', 'coupon',  'time',
                    'coordinate', 'fee', 'tripStatus','zone','vehicle.model','fare_biddings','parcel','parcelUserInfo'])
                ->where(['type' => 'parcel', $attributes['column'] => $attributes['value']])
                ->when($attributes['whereNotNull'] ?? null, fn($query) => $query->whereNotNull($attributes['whereNotNull']))
                ->where(function($query){
                    $query->where(function($query1){
                        $query1->where('current_status', 'completed')
                        ->where('payment_status',UNPAID);
                    })->orWhere(function($query){
                        $query->whereIn('current_status', [PENDING,ACCEPTED,ONGOING]);
                    });
                })
                ->paginate(perPage: $attributes['limit'], page: $attributes['offset']);
        }

        return $this->trip->query()
            ->with(['customer','driver','vehicleCategory','vehicleCategory.tripFares','vehicle', 'coupon',  'time',
                'coordinate', 'fee', 'tripStatus','zone','vehicle.model','fare_biddings','parcel','parcelUserInfo'])
            ->where(['type' => 'parcel', $attributes['column'] => $attributes['value']])
            ->when($attributes['whereNotNull'] ?? null, fn($query) => $query->whereNotNull($attributes['whereNotNull']))
            ->whereNotIn('current_status', ['cancelled', 'completed'])
            ->paginate(perPage: $attributes['limit'], page: $attributes['offset']);

    }

    /**
     * @return Builder|Model|TripRequest|object|null
     */
    public function unpaidParcelRequest(array $attributes)
    {
        return $this->trip->query()
            ->with(['customer','driver','vehicleCategory','vehicleCategory.tripFares','vehicle', 'coupon',  'time',
                'coordinate', 'fee', 'tripStatus','zone','vehicle.model','fare_biddings','parcel','parcelUserInfo'])
            ->whereNotNull('driver_id')
            ->where([
                'type' => 'parcel',
                $attributes['column'] => $attributes['value'],
                'payment_status' => UNPAID
                ])
            ->when($attributes['whereHas'] ?? null, fn($query) => $query->whereHas('parcel',
                fn($query) => $query->where('payer', 'sender')))
            ->paginate(perPage: $attributes['limit'], page: $attributes['offset']);
    }

    public function getPopularTips()
    {
        return $this->trip->query()->whereNot('tips',0)->groupBy('tips')->selectRaw('tips, count(*) as total')->orderBy('total', 'desc')->first();
    }

}

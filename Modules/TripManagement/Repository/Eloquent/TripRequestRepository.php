<?php

namespace Modules\TripManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Modules\TripManagement\Entities\TripRequest;
use Modules\TripManagement\Repository\TripRequestRepositoryInterface;

class TripRequestRepository extends BaseRepository implements TripRequestRepositoryInterface
{
    public function __construct(TripRequest $model)
    {
        parent::__construct($model);
    }

    public function calculateCouponAmount($startDate = null, $endDate = null, $startTime = null, $month = null, $year = null): mixed
    {
        $query = $this->model->whereNotNull('coupon_amount');

        if ($startDate !== null && $endDate !== null) {
            $query->whereBetween('created_at', [
                "{$startDate->format('Y-m-d')} 00:00:00",
                "{$endDate->format('Y-m-d')} 23:59:59"
            ]);
        } elseif ($startTime !== null) {
            $query->whereBetween('created_at', [
                date('Y-m-d', strtotime('today')) . ' ' . date('H:i:s', $startTime),
                date('Y-m-d', strtotime('today')) . ' ' . date('H:i:s', strtotime('+2 hours', $startTime))
            ]);
        } elseif ($month !== null) {
            $query->whereMonth('created_at', $month)
                ->whereYear('created_at', now()->format('Y'));
        } elseif ($year !== null) {
            $query->whereYear('created_at', $year);
        } else {
            $query->whereDay('created_at', now()->format('d'))
                ->whereMonth('created_at', now()->format('m'));
        }

        return $query->sum('coupon_amount');
    }

    public function fetchTripData($dateRange): Collection
    {
        $query = $this->model->whereNotNull('coupon_amount');

        switch ($dateRange) {
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $query->whereBetween('created_at', [$startDate, $endDate]);
                break;

            case 'this_month':
                $query->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month);
                break;

            case 'this_year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'today':
                $query->whereDate('created_at', Carbon::today());
            default:
                $query;
                break;
        }

        return $query->get();
    }


    public function statusWiseTotalTripRecords(array $attributes): Collection
    {
        return $this->model->query()
            ->when($attributes['from'] ?? null, fn($query) => $query->whereBetween('created_at', [$attributes['from'], $attributes['to']]))
            ->selectRaw('current_status, count(*) as total_records')
            ->groupBy('current_status')->get();
    }


    public function pendingParcelList(array $attributes)
    {
        return $this->model->query()
            ->with([
                'customer', 'driver', 'vehicleCategory', 'vehicleCategory.tripFares', 'vehicle', 'coupon', 'time',
                'coordinate', 'fee', 'tripStatus', 'zone', 'vehicle.model', 'fare_biddings', 'parcel', 'parcelUserInfo'
            ])
            ->where(['type' => 'parcel', $attributes['column'] => $attributes['value']])
            ->when($attributes['whereNotNull'] ?? null, fn($query) => $query->whereNotNull($attributes['whereNotNull']))
            ->whereNotIn('current_status', ['cancelled', 'completed'])
            ->paginate(perPage: $attributes['limit'], page: $attributes['offset']);
    }


    public function updateRelationalTable($attributes): mixed
    {
        $trip = $this->findOne(id: $attributes['value']);

        if ($attributes['trip_status'] ?? null) {
            $tripData['current_status'] = $attributes['trip_status'];

            $trip->update($tripData);
            $trip->tripStatus()->update([
                $attributes['trip_status'] => now()
            ]);
        }
        if ($attributes['driver_id'] ?? null) {
            $trip->driver_id = null;
            $trip->save();
        }

        if ($attributes['coordinate'] ?? null) {
            $trip->coordinate()->update($attributes['coordinate']);
        }
        if ($attributes['fee'] ?? null) {
            $trip->fee()->update($attributes['fee']);
        }
        return $trip->tripStatus;
    }


    public function findOneWithAvg(array $criteria = [], array $relations = [], array $withCountQuery = [], bool $withTrashed = false, bool $onlyTrashed = false, array $withAvgRelation = []): ?Model
    {
        $data = $this->prepareModelForRelationAndOrder(relations: $relations)
            ->where($criteria)
            ->when(!empty($withCountQuery), function ($query) use ($withCountQuery) {
                $this->withCountQuery($query, $withCountQuery);
            })
            ->when(($onlyTrashed || $withTrashed), function ($query) use ($onlyTrashed, $withTrashed) {
                $this->withOrWithOutTrashDataQuery($query, $onlyTrashed, $withTrashed);
            })
            ->when(!empty($withAvgRelation), function ($query) use ($withAvgRelation) {
                $query->withAvg($withAvgRelation[0], $withAvgRelation[1]);
            })
            ->first();
        return $data;
    }


    public function getWithAvg(array $criteria = [], array $searchCriteria = [], array $whereInCriteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = [], array $withAvgRelation = [], array $whereBetweenCriteria = [], array $whereNotNullCriteria = []): Collection|LengthAwarePaginator
    {

        $model = $this->prepareModelForRelationAndOrder(relations: $relations, orderBy: $orderBy)
            ->when(!empty($criteria), function ($whereQuery) use ($criteria) {
                $whereQuery->where($criteria);
            })->when(!empty($whereInCriteria), function ($whereInQuery) use ($whereInCriteria) {
                foreach ($whereInCriteria as $column => $values) {
                    $whereInQuery->whereIn($column, $values);
                }
            })->when(!empty($searchCriteria), function ($whereQuery) use ($searchCriteria) {
                $this->searchQuery($whereQuery, $searchCriteria);
            })->when(($onlyTrashed || $withTrashed), function ($query) use ($onlyTrashed, $withTrashed) {
                $this->withOrWithOutTrashDataQuery($query, $onlyTrashed, $withTrashed);
            })
            ->when(!empty($withCountQuery), function ($query) use ($withCountQuery) {
                $this->withCountQuery($query, $withCountQuery);
            })
            ->when(!empty($whereBetweenCriteria), function ($whereQuery) use ($whereBetweenCriteria) {
                foreach ($whereBetweenCriteria as $column => $values) {
                    $whereQuery->whereBetween($column, $values);
                }
            })
            ->when(!empty($whereNotNullCriteria), function ($whereQuery) use ($whereNotNullCriteria) {
                foreach ($whereNotNullCriteria as $column) {
                    $whereQuery->whereNotNull($column);
                }
            })
            ->when(!empty($withAvgRelation), function ($query) use ($withAvgRelation) {
                $query->withAvg($withAvgRelation[0], $withAvgRelation[1]);
            });

        if ($limit) {
            return !empty($criteria) ? $model->paginate($limit)->appends($criteria) : $model->paginate($limit);
        }
        return $model->get();
    }


    public function getPendingRides($attributes): mixed
    {
        return $this->model->query()
            ->when($attributes['relations'] ?? null, fn($query) => $query->with($attributes['relations']))
            ->with([
                'fare_biddings' => fn($query) => $query->where('driver_id', auth()->id()),
                'coordinate' => fn($query) => $query->distanceSphere('pickup_coordinates', $attributes['driver_locations'], $attributes['distance'])
            ])
            ->whereHas('coordinate',
                fn($query) => $query->distanceSphere('pickup_coordinates', $attributes['driver_locations'], $attributes['distance']))
            ->when($attributes['withAvgRelation'] ?? null,
                fn($query) => $query->withAvg($attributes['withAvgRelation'], $attributes['withAvgColumn']))
            ->whereDoesntHave('ignoredRequests', fn($query) => $query->where('user_id', auth()->id()))
            ->where(fn($query) => $query->where('vehicle_category_id', $attributes['vehicle_category_id'])
                ->orWhereNull('vehicle_category_id')
            )
            ->where(['zone_id' => $attributes['zone_id'], 'current_status' => PENDING,])
            ->orderBy('created_at', 'desc')
            ->paginate(perPage: $attributes['limit'], page: $attributes['offset']);
    }

    public function getZoneWiseStatistics(array $criteria = [], array $searchCriteria = [], array $whereInCriteria = [], array $whereBetweenCriteria = [], array $whereHasRelations = [], array $withAvgRelations = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = [], array $appends = []): Collection|LengthAwarePaginator
    {
        $model = $this->prepareModelForRelationAndOrder(relations: $relations, orderBy: $orderBy)
            ->when(!empty($criteria), function ($whereQuery) use ($criteria) {
                $whereQuery->where($criteria);
            })->when(!empty($whereInCriteria), function ($whereInQuery) use ($whereInCriteria) {
                foreach ($whereInCriteria as $column => $values) {
                    $whereInQuery->whereIn($column, $values);
                }
            })->when(!empty($whereHasRelations), function ($whereHasQuery) use ($whereHasRelations) {
                foreach ($whereHasRelations as $relation => $conditions) {
                    $whereHasQuery->whereHas($relation, function ($query) use ($conditions) {
                        $query->where($conditions);
                    });
                }
            })->when(!empty($whereBetweenCriteria), function ($whereBetweenQuery) use ($whereBetweenCriteria) {
                foreach ($whereBetweenCriteria as $column => $range) {
                    $whereBetweenQuery->whereBetween($column, $range);
                }
            })->when(!empty($searchCriteria), function ($whereQuery) use ($searchCriteria) {
                $this->searchQuery($whereQuery, $searchCriteria);
            })->when(($onlyTrashed || $withTrashed), function ($query) use ($onlyTrashed, $withTrashed) {
                $this->withOrWithOutTrashDataQuery($query, $onlyTrashed, $withTrashed);
            })
            ->when(!empty($withCountQuery), function ($query) use ($withCountQuery) {
                $this->withCountQuery($query, $withCountQuery);
            })->when(!empty($withAvgRelations), function ($query) use ($withAvgRelations) {
                foreach ($withAvgRelations as $relation) {
                    $query->withAvg($relation);
                }
            })->whereNotNull('zone_id')
            ->selectRaw('count(completed) as completed_trips,count(cancelled) as cancelled_trips,count(pending) as pending_trips,count(accepted) as accepted_trips,count(ongoing) as ongoing_trips,zone_id, count(*) as total_records')
            ->groupBy('zone_id')->orderBy('total_records', 'asc');
        if ($limit) {
            return !empty($appends) ? $model->paginate($limit)->appends($appends) : $model->paginate($limit);
        }
        return $model->get();
    }

    public function getZoneWiseEarning(array $criteria = [], array $searchCriteria = [], array $whereInCriteria = [], array $whereBetweenCriteria = [], array $whereHasRelations = [], array $withAvgRelations = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = [], array $appends = [], $startDate = null, $endDate = null, $startTime = null, $month = null, $year = null): Collection|LengthAwarePaginator
    {
        $model = $this->prepareModelForRelationAndOrder(relations: $relations, orderBy: $orderBy)
            ->when(!empty($criteria), function ($whereQuery) use ($criteria) {
                $whereQuery->where($criteria);
            })->when(!empty($whereInCriteria), function ($whereInQuery) use ($whereInCriteria) {
                foreach ($whereInCriteria as $column => $values) {
                    $whereInQuery->whereIn($column, $values);
                }
            })->when(!empty($whereHasRelations), function ($whereHasQuery) use ($whereHasRelations) {
                foreach ($whereHasRelations as $relation => $conditions) {
                    $whereHasQuery->whereHas($relation, function ($query) use ($conditions) {
                        $query->where($conditions);
                    });
                }
            })->when(!empty($searchCriteria), function ($whereQuery) use ($searchCriteria) {
                $this->searchQuery($whereQuery, $searchCriteria);
            })->when(($onlyTrashed || $withTrashed), function ($query) use ($onlyTrashed, $withTrashed) {
                $this->withOrWithOutTrashDataQuery($query, $onlyTrashed, $withTrashed);
            })
            ->when(!empty($withCountQuery), function ($query) use ($withCountQuery) {
                $this->withCountQuery($query, $withCountQuery);
            })->when(!empty($withAvgRelations), function ($query) use ($withAvgRelations) {
                foreach ($withAvgRelations as $relation) {
                    $query->withAvg($relation);
                }
            });
        if ($startDate !== null && $endDate !== null) {
            $model->whereBetween('created_at', [
                "{$startDate->format('Y-m-d')} 00:00:00",
                "{$endDate->format('Y-m-d')} 23:59:59"
            ]);
        } elseif ($startDate !== null && $startTime !== null) {
            $model->whereBetween('created_at', [
                date('Y-m-d', strtotime($startDate)) . ' ' . date('H:i:s', $startTime),
                date('Y-m-d', strtotime($startDate)) . ' ' . date('H:i:s', strtotime('+2 hours', $startTime))
            ]);
        } elseif ($month !== null) {
            $model->whereMonth('created_at', $month)
                ->whereYear('created_at', now()->format('Y'));
        } elseif ($year !== null) {
            $model->whereYear('created_at', $year);
        } else {
            $model->whereDay('created_at', now()->format('d'))
                ->whereMonth('created_at', now()->format('m'));
        }
        if ($limit) {
            return !empty($appends) ? $model->paginate($limit)->appends($appends) : $model->paginate($limit);
        }
        return $model->get();
    }

    public function getLeaderBoard(string $userType, array $criteria = [], array $searchCriteria = [], array $whereInCriteria = [], array $whereBetweenCriteria = [], array $whereHasRelations = [], array $withAvgRelations = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = [], array $appends = []): Collection|LengthAwarePaginator
    {
        $model = $this->prepareModelForRelationAndOrder(relations: $relations, orderBy: $orderBy)
            ->when(!empty($criteria), function ($whereQuery) use ($criteria) {
                $whereQuery->where($criteria);
            })->when(!empty($whereInCriteria), function ($whereInQuery) use ($whereInCriteria) {
                foreach ($whereInCriteria as $column => $values) {
                    $whereInQuery->whereIn($column, $values);
                }
            })
            ->when(!empty($whereHasRelations), function ($whereHasQuery) use ($whereHasRelations) {
                foreach ($whereHasRelations as $relation => $conditions) {
                    $whereHasQuery->whereHas($relation, function ($query) use ($conditions) {
                        $query->where($conditions);
                    });
                }
            })->when(!empty($whereBetweenCriteria), function ($whereBetweenQuery) use ($whereBetweenCriteria) {
                foreach ($whereBetweenCriteria as $column => $range) {
                    $whereBetweenQuery->whereBetween($column, $range);
                }
            })->when(!empty($searchCriteria), function ($whereQuery) use ($searchCriteria) {
                $this->searchQuery($whereQuery, $searchCriteria);
            })->when(($onlyTrashed || $withTrashed), function ($query) use ($onlyTrashed, $withTrashed) {
                $this->withOrWithOutTrashDataQuery($query, $onlyTrashed, $withTrashed);
            })
            ->when(!empty($withCountQuery), function ($query) use ($withCountQuery) {
                $this->withCountQuery($query, $withCountQuery);
            })->when(!empty($withAvgRelations), function ($query) use ($withAvgRelations) {
                foreach ($withAvgRelations as $relation) {
                    $query->withAvg($relation);
                }
            })->whereNotNull($userType)
            ->selectRaw($userType . ', count(*) as total_records ,SUM(paid_fare) as income')
            ->groupBy($userType)
            ->orderBy('total_records', 'desc');
        if ($limit) {
            return !empty($appends) ? $model->paginate($limit)->appends($appends) : $model->paginate($limit);
        }
        return $model->get();
    }
}

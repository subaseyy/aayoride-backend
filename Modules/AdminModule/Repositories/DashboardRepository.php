<?php

namespace Modules\AdminModule\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\AdminModule\Interfaces\DashboardInterface;
use Modules\TripManagement\Entities\TripRequest;
use Modules\ZoneManagement\Entities\Zone;

class DashboardRepository implements DashboardInterface
{
    public function __construct(
        protected Zone $zone
    )
    {
    }

    public function getZoneWithcenter()
    {
        return $this->zone->selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")->latest()->first();
    }

    public function leaderBoard($attributes)
    {
        $user = $attributes['type'];
        $user == 'customer' ? $id = 'customer_id' : $id = 'driver_id';
        return TripRequest::query()
            ->with($user)
            ->whereNotNull($id)
            ->selectRaw($id . ', count(*) as total_records')
            ->groupBy($id)
            ->orderBy('total_records', 'desc')
            ->when($attributes['date'] ?? null, fn($query) => $query->whereBetween('created_at', [$attributes['date']['start'], $attributes['date']['end']]))
            ->take(6)
            ->get();

    }

    public function adminEarning($attributes)
    {
        return TripRequest::query()
            ->when($attributes['date'] ?? null, fn($query) => $query->whereBetween('created_at', [$attributes['date']['start'], $attributes['date']['end']]))
            ->when($attributes['zone'] && $attributes['zone'] != 'all', fn($query) => $query->where('zone_id', $attributes['zone']))
            ->select(
                DB::raw('IFNULL(count(id),0) as sums'),
                DB::raw('TIME(created_at) time')
            )
            ->when($attributes['current_status'] ?? null, fn($query) => $query->where(['current_status' => 'completed']))
            ->groupby('time')
            ->get()
            ->toArray();
    }

    public function zoneStatistics($attributes)
    {
        $records = TripRequest::query()
            ->when($attributes['zone'] != 'all', fn($query) => $query->where('zone_id', $attributes['zone']))
            ->select('zone_id')
            ->with(['zone'])
            ->whereNotNull('zone_id')
            ->selectRaw('zone_id, count(*) as total_records')
            ->groupBy('zone_id')->orderBy('total_records', 'asc')
            ->when($attributes['date'] ?? null, fn($query) => $query->whereBetween('created_at', [$attributes['date']['start'], $attributes['date']['end']]))
            ->get();

        $count = TripRequest::query()->count();
        return [
            'records' => $records,
            'count' => $count
        ];
    }
}

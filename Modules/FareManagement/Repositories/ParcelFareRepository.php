<?php

namespace Modules\FareManagement\Repositories;



use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\FareManagement\Entities\ParcelFare;
use Modules\FareManagement\Entities\ParcelFareWeight;
use Modules\FareManagement\Interfaces\ParcelFareInterface;

class ParcelFareRepository implements ParcelFareInterface
{
    public function __construct(
        private ParcelFare $fare
    )
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $query = $this->fare
            ->query()
            ->when(!empty($relations), fn($query) => $query->with($relations))
            ->when(array_key_exists('query', $attributes), function ($query) use($attributes){
                $query->where($attributes['query'], $attributes['value']);
            });
        if ($dynamic_page) {
            return $query->paginate($limit, ["*"], $offset);
        }
        return $query->paginate($limit);
    }

    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
         return ($this->fare
            ->query()
            ->where($column, $value)
             ->when(array_key_exists('relations', $attributes), function ($query) use($attributes){
                 $query->with($attributes['relations']);
             })
            ->when(array_key_exists('fares', $attributes), function($query) use($attributes){
                $query->whereHas('fares', function($query) use($attributes){
                    $query->where(['parcel_category_id' => $attributes['fares']['cat_id'], 'parcel_weight_id' => $attributes['fares']['weight_id']]);
                });
            })
            ->first());
    }

    public function store(array $attributes): Model
    {
        DB::beginTransaction();
        $fare = $this->getBy(column: 'zone_id', value: $attributes['zone_id']);
        if (is_null($fare)) {
            $fare = $this->fare;
        }
        $fare->zone_id = $attributes['zone_id'];
        $fare->base_fare = $attributes['base_fare'];
        $fare->base_fare_per_km = 0;
        $fare->cancellation_fee_percent = 0;
        $fare->min_cancellation_fee = 0;
        $fare->save();

        $fare->fares()->delete();
        foreach ($attributes['parcel_category'] as $category) {
            if (array_key_exists('weight_' . $category, $attributes)) {
                foreach ($attributes['parcel_weight'] as $weight) {
                    if (array_key_exists($weight['id'], $attributes['weight_' . $category])) {
                        $fare->fares()->create([
                            'parcel_weight_id' => $weight->id,
                            'parcel_category_id' => $category,
                            'base_fare' => $attributes['base_fare_' . $category]??0,
                            'fare_per_km' => $attributes['weight_' . $category][$weight->id]??0,
                            'zone_id' => $attributes['zone_id']
                        ]);
                    }
                }
            }
        }
        DB::commit();
        return $fare;
    }

    public function update(array $attributes, string $id): Model
    {

    }

    public function destroy(string $id): Model
    {
        // TODO: Implement destroy() method.
    }

    /**
     * From ParcelFareWeight table get parcel fares by vehicle cat id, weight id and zone id
     * @param array $attributes
     * @param array $relations
     * @return mixed
     */
    public function categorizedFares(array $attributes,array $relations = []):mixed
    {

        return ParcelFareWeight::when(!empty($relations), function($query) use($relations){
            $query->with($relations);
        })
        ->when(!is_null($attributes['parcel_weight_id']), function($query) use($attributes){
            return $query->where(['parcel_category_id' => $attributes['parcel_category_id'], 'parcel_weight_id' => $attributes['parcel_weight_id']]);
        })
        ->when(is_null($attributes['parcel_weight_id']), function($query) use($attributes){
            return $query->where('parcel_category_id' , $attributes['parcel_category_id']);
        })
        ->whereZoneId($attributes['zone_id'])
        ->get();

    }

    public function getZoneFare(array $attributes): mixed
    {
        return $this->fare
        ->query()
        ->when(array_key_exists('column', $attributes), fn ($query) =>
            $query->where($attributes['column'], $attributes['value'])
        )
        ->with(['fares' => function($query) use($attributes){
            $query->where('parcel_weight_id', $attributes['parcel_weight_id'])
            ->where('zone_id', $attributes['value'])
            ->where('parcel_category_id', $attributes['parcel_category_id'])
            ;
        }, 'zone'] )
        ->whereHas('fares', function ($query) use ($attributes){
            $query->where('parcel_weight_id', $attributes['parcel_weight_id'])
                ->where('zone_id', $attributes['value'])
                ->where('parcel_category_id', $attributes['parcel_category_id'])
            ;
        })
        ->first();
    }
}

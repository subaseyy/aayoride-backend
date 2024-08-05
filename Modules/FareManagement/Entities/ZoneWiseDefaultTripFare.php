<?php

namespace Modules\FareManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ZoneManagement\Entities\Zone;

class ZoneWiseDefaultTripFare extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'zone_id',
        'base_fare',
        'base_fare_per_km',
        'waiting_fee_per_min',
        'cancellation_fee_percent',
        'min_cancellation_fee',
        'idle_fee_per_min',
        'trip_delay_fee_per_min',
        'penalty_fee_for_cancel',
        'fee_add_to_next',
        'category_wise_different_fare',
        'pickup_bonus_amount',
        'minimum_pickup_distance',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'base_fare'=>'double',
        'base_fare_per_km'=>'double',
        'waiting_fee_per_min'=>'double',
        'cancellation_fee_percent'=>'double',
        'min_cancellation_fee'=>'double',
        'idle_fee_per_min'=>'double',
        'trip_delay_fee_per_min'=>'double',
        'penalty_fee_for_cancel'=>'double',
        'fee_add_to_next'=>'double',
        'category_wise_different_fare'=>'integer'
    ];
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function tripFares(): HasMany
    {
        return $this->hasMany(TripFare::class,'zone_wise_default_trip_fare_id');
    }

    protected static function newFactory()
    {
        return \Modules\FareManagement\Database\factories\ZoneWiseDefaultTripFareFactory::new();
    }
}

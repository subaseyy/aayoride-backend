<?php

namespace Modules\FareManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ZoneManagement\Entities\Zone;

class ParcelFare extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'zone_id',
        'base_fare',
        'base_fare_per_km',
        'cancellation_fee_percent',
        'min_cancellation_fee',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'base_fare' => 'float',
        'base_fare_per_km' => 'float',
        'cancellation_fee_percent' => 'float',
        'min_cancellation_fee' => 'float',
    ];

    protected static function newFactory()
    {
        return \Modules\FareManagement\Database\factories\ParcelFareFactory::new();
    }

    public function fares()
    {
        return $this->hasMany(ParcelFareWeight::class, 'parcel_fare_id');
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }
}

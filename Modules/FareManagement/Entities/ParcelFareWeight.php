<?php

namespace Modules\FareManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ParcelManagement\Entities\ParcelCategory;
use Modules\ParcelManagement\Entities\ParcelWeight;
use Modules\ZoneManagement\Entities\Zone;

class ParcelFareWeight extends Model
{
    use HasFactory;

    protected $table = 'parcel_fares_parcel_weights';
    protected $fillable = [
        'parcel_fare_id',
        'parcel_weight_id',
        'parcel_category_id',
        'base_fare',
        'fare_per_km',
        'zone_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'fare' => 'float'
    ];

    public function parcelFare()
    {
        return $this->belongsTo(ParcelFare::class);
    }

    public function parcelWeight()
    {
        return $this->belongsTo(ParcelWeight::class);
    }

    public function parcelCategory()
    {
        return $this->belongsTo(ParcelCategory::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    protected static function newFactory()
    {
        return \Modules\FareManagement\Database\factories\ParcelFareWeightFactory::new();
    }
}

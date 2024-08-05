<?php

namespace Modules\ParcelManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TripManagement\Entities\TripRequest;
use Modules\TripManagement\Entities\TripStatus;

class ParcelInformation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parcel_category_id',
        'trip_request_id',
        'payer',
        'weight',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'weight' => 'double',
    ];

    protected static function newFactory()
    {
        return \Modules\TripManagement\Database\factories\ParcelInformationFactory::new();
    }

    public function trip(){
        return $this->belongsTo(TripRequest::class, 'trip_request_id');
    }

    public function tripStatus(){
        return $this->hasOne(TripStatus::class, 'trip_request_id', 'trip_request_id');
    }

    public function parcelCategory(){
        return $this->belongsTo(ParcelCategory::class);
    }
}

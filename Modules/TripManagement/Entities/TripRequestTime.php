<?php

namespace Modules\TripManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TripRequestTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_request_id',
        'estimated_time',
        'actual_time',
        'waiting_time',
        'delay_time',
        'idle_timestamp',
        'idle_time',
        'driver_arrival_time',
        'driver_arrival_timestamp',
        'driver_arrives_at',
        'customer_arrives_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'estimated_time' => 'float',
        'actual_time' => 'float',
        'waiting_time' => 'float',
        'delay_time' => 'float',
        'idle_time' => 'float',
        'driver_arrival_time' => 'float',
    ];

    protected static function newFactory()
    {
        return \Modules\TripManagement\Database\factories\TripRequestTimeFactory::new();
    }

    public function tripRequest()
    {
        $this->belongsTo(TripRequest::class, 'trip_request_id');
    }
}

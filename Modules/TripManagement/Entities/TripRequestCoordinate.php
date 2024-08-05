<?php

namespace Modules\TripManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class TripRequestCoordinate extends Model
{
    use HasFactory, HasSpatial;

    protected $fillable = [
        'trip_request_id',
        'pickup_coordinates',
        'pickup_address',
        'destination_coordinates',
        'is_reached_destination',
        'destination_address',
        'intermediate_coordinates',
        'int_coordinate_1',
        'is_reached_1',
        'int_coordinate_2',
        'is_reached_2',
        'intermediate_addresses',
        'start_coordinates',
        'drop_coordinates',
        'driver_accept_coordinates',
        'customer_request_coordinates',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'pickup_coordinates' => Point::class,
        'destination_coordinates' => Point::class,
        'start_coordinates' => Point::class,
        'drop_coordinates' => Point::class,
        'driver_accept_coordinates' => Point::class,
        'customer_request_coordinates' => Point::class,
        'int_coordinate_1' => Point::class,
        'int_coordinate_2' => Point::class,
        'intermediate_coordinates' => 'array',
        'intermediate_addresses' => 'array',
        'is_reached_destination' => 'boolean',
        'is_reached_1' => 'boolean',
        'is_reached_2' => 'boolean'
    ];

    public function tripRequest()
    {
        return $this->belongsTo(TripRequest::class, 'trip_request_id');
    }

    public function scopeDistanceSphere($query, $column, $location, $distance)
    {
        return $query->whereRaw("ST_Distance_Sphere($column, POINT($location->longitude, $location->latitude)) < $distance");
    }

    protected static function newFactory()
    {
        return \Modules\TripManagement\Database\factories\TripRequestCoordinateFactory::new();
    }
}

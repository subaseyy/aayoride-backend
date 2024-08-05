<?php

namespace Modules\TripManagement\Entities;

use App\Traits\HasUuid;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class TripRoute extends Model
{
    use HasFactory,HasSpatial;

    protected $fillable = [
        'trip_request_id',
        'coordinates',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'coordinates' => Polygon::class
    ];

    protected static function newFactory()
    {
        return \Modules\TripManagement\Database\factories\TripRouteFactory::new();
    }

    public function tripRequest()
    {
        return $this->belongsTo(TripRequest::class);
    }
}

<?php

namespace Modules\TripManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FareBiddingLog extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'trip_request_id',
        'driver_id',
        'customer_id',
        'bid_fare',
        'is_ignored',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'bid_fare' => 'float',
        'is_ignored' => 'boolean',
    ];

    protected static function newFactory()
    {
        return \Modules\TripManagement\Database\factories\FareBiddingLogFactory::new();
    }


}

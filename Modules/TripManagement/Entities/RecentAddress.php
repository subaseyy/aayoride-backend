<?php

namespace Modules\TripManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Modules\UserManagement\Entities\User;
use Modules\ZoneManagement\Entities\Zone;

class RecentAddress extends Model
{
    use HasFactory, HasSpatial;

    protected $fillable = [
        'user_id',
        'zone_id',
        'pickup_coordinates',
        'pickup_address',
        'destination_coordinates',
        'destination_address',
        'created_at',
        'updated_at',
    ];
    protected $cast = [
        'pickup_coordinates' => Point::class,
        'destination_coordinates' => Point::class
    ];

    protected static function newFactory()
    {
        return \Modules\TripManagement\Database\factories\RecentAddressFactory::new();
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }
}

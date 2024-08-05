<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\VehicleManagement\Entities\Vehicle;
use Modules\ZoneManagement\Entities\Zone;

class UserLastLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'latitude',
        'longitude',
        'zone_id',
        'created_at',
        'updated_at',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function driverDetails()
    {
        return $this->belongsTo(DriverDetail::class, 'user_id', 'user_id');
    }

    public function vehicle(){
        return $this->belongsTo(Vehicle::class, 'user_id', 'driver_id');
    }

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\UserLastLocationFactory::new();
    }
}

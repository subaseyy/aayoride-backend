<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DriverTimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'date',
        'online',
        'offline',
        'online_time',
        'accepted',
        'completed',
        'start_driving',
        'on_driving_time',
        'idle_time',
        'on_time_completed',
        'late_completed',
        'late_pickup',
        'created_at',
        'updated_at',
    ];

    protected $casts =[
        "online_time" => "float",
        "idle_time" => "float",
        "on_driving_time" => "float",
    ];

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\DriverTimeLogFactory::new();
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}

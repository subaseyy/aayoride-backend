<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'time_track_id',
        'online_at',
        'offline_at',
        'created_at',
        'updated_at',
    ];

    protected static function newFactory()
    {
        return \Modules\TripManagement\Database\factories\TimeLogFactory::new();
    }
}

<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'total_online',
        'total_offline',
        'total_idle',
        'total_driving',
        'last_ride_started_at',
        'last_ride_completed_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'total_online'=>'integer',
        'total_offline'=>'integer',
        'total_idle'=>'integer',
        'total_driving'=>'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\TimeTrackFactory::new();
    }

    public function logs(){
        return $this->hasMany(TimeLog::class);
    }
    public function latestLog(){
        return $this->hasOne(TimeLog::class)->latestOfMany();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item){
            $details = DriverDetail::query()->firstWhere('user_id', $item->user_id);
            $details->availability_status = 'available';
            $details->is_online = true;
            $details->save();
        });
    }
}

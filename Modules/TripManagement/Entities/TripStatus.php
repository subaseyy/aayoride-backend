<?php

namespace Modules\TripManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\UserManagement\Entities\User;

class TripStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_request_id',
        'customer_id',
        'driver_id',
        'pending',
        'accepted',
        'out_for_pickup',
        'picked_up',
        'ongoing',
        'completed',
        'cancelled',
        'failed',
        'note',
        'created_at',
        'updated_at',
    ];
    protected $table = 'trip_status';

    protected static function newFactory()
    {
        return \Modules\TripManagement\Database\factories\TripStatusFactory::new();
    }

    public function tripRequest()
    {
        return $this->belongsTo(TripRequest::class);
    }
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}

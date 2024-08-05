<?php

namespace Modules\TripManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TripRequestFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_request_id',
        'cancellation_fee',
        'cancelled_by',
        'waiting_fee',
        'waited_by',
        'idle_fee',
        'delay_fee',
        'delayed_by',
        'vat_tax',
        'tips',
        'admin_commission',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'cancellation_fee' => 'float',
        'waiting_fee' => 'float',
        'idle_fee' => 'float',
        'delay_fee' => 'float',
        'vat_tax' => 'float',
        'tips' => 'float',
        'admin_commission' => 'float'
    ];

    public function tripRequest()
    {
        return $this->belongsTo(TripRequest::class, 'trip_request_id');
    }

    protected static function newFactory()
    {
        return \Modules\TripManagement\Database\factories\TripRequestFeeFactory::new();
    }
}

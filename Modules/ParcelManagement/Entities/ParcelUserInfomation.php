<?php

namespace Modules\ParcelManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TripManagement\Entities\TripRequest;

class ParcelUserInfomation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trip_request_id',
        'contact_number',
        'name',
        'address',
        'user_type',
        'created_at',
        'updated_at',
    ];



    protected static function newFactory()
    {
        return \Modules\TripManagement\Database\factories\ParcelUserInfomationFactory::new();
    }

    public function trip()
    {
        return $this->belongsTo(TripRequest::class, 'trip_request_id');
    }

}

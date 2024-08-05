<?php

namespace Modules\ReviewModule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TripManagement\Entities\TripRequest;
use Modules\UserManagement\Entities\User;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_request_id',
        'given_by',
        'received_by',
        'trip_type',
        'rating',
        'feedback',
        'images',
        'is_saved',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'images' => 'array',
        'is_saved' => 'boolean',
    ];

    protected static function newFactory()
    {
        return \Modules\ReviewModule\Database\factories\ReviewFactory::new();
    }

    public function customer()
    {
        return $this->belongsTo(User::class,'user_id', 'id')->where(['user_type' => CUSTOMER]);
    }

    public function givenUser(){
        return $this->belongsTo(User::class,'given_by')->withTrashed();
    }

    public function recievedUser(){
        return $this->belongsTo(User::class,'received_by')->withTrashed();
    }

    public function trip(){
        return $this->belongsTo(TripRequest::class,'trip_request_id');
    }

}

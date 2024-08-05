<?php

namespace Modules\TripManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\UserManagement\Entities\User;

class RejectedDriverRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_request_id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected static function newFactory()
    {
        return \Modules\TripManagement\Database\factories\RejectedDriverRequestFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}

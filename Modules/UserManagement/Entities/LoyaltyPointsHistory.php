<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoyaltyPointsHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'model',
        'model_id',
        'points',
        'type',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'points'=>'double'
    ];

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\LoyaltyPointsHistoryFactory::new();
    }
}

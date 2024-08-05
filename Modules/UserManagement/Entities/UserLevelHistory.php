<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserLevelHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_level_id',
        'user_id',
        'user_type',
        'completed_ride',
        'ride_reward_status',
        'total_amount',
        'amount_reward_status',
        'cancellation_rate',
        'cancellation_reward_status',
        'reviews',
        'reviews_reward_status',
        'is_level_reward_granted',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'completed_ride'=>'integer',
        'ride_reward_status'=>'boolean',
        'total_amount'=>'float',
        'amount_reward_status'=>'boolean',
        'cancellation_rate'=>'float',
        'cancellation_reward_status'=>'boolean',
        'reviews'=>'integer',
        'reviews_reward_status'=>'boolean',
        'is_level_reward_granted'=>'boolean'
    ];

    public function user() {
        return $this->belongsToMany(User::class);
    }
    public function userLevel() {
        return $this->belongsToMany(UserLevel::class);
    }

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\UserLevelHistoryFactory::new();
    }
}

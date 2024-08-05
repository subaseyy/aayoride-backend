<?php

namespace Modules\UserManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Modules\AdminModule\Entities\ActivityLog;
use Modules\TripManagement\Entities\TripStatus;

class UserLevel extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'sequence',
        'name',
        'reward_type',
        'reward_amount',
        'image',
        'targeted_ride',
        'targeted_ride_point',
        'targeted_amount',
        'targeted_amount_point',
        'targeted_cancel',
        'targeted_cancel_point',
        'targeted_review',
        'targeted_review_point',
        'user_type',
        'is_active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'sequence' => 'integer',
        'reward_amount'=>'string',
        'targeted_ride'=>'double',
        'targeted_ride_point'=>'double',
        'targeted_amount'=>'double',
        'targeted_amount_point'=>'double',
        'targeted_cancel'=>'double',
        'targeted_cancel_point'=>'double',
        'targeted_review'=>'double',
        'targeted_review_point'=>'double',
        'is_active'=> 'integer'
    ];

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\UserLevelFactory::new();
    }

    public function scopeUserType($query, $type){
        $query->where('user_type', $type);
    }

    public function access()
    {
        return $this->hasOne(LevelAccess::class, 'level_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'user_level_id');
    }

    public function logs ()
    {
        return $this->morphMany(ActivityLog::class, 'logable');
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function($item) {
            $array = [];
            foreach ($item->changes as $key => $change){
                $array[$key] = $item->original[$key];
            }
            if(!empty($array)) {
                $log = new ActivityLog();
                $log->edited_by = auth()->user()->id ?? 'user_update';
                $log->before = $array;
                $log->after = $item->changes;
                $log->user_type = $item->user_type;
                $item->logs()->save($log);
            }
        });

        static::deleted(function($item) {
            $log = new ActivityLog();
            $log->edited_by = auth()->user()->id;
            $log->before = $item->original;
            $log->user_type = $item->user_type;
            $item->logs()->save($log);
        });

    }

}

<?php

namespace Modules\PromotionManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AdminModule\Entities\ActivityLog;
use Modules\TripManagement\Entities\TripRequest;
use Modules\VehicleManagement\Entities\VehicleCategory;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\UserLevel;

class CouponSetup extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'user_level_id',
        'min_trip_amount',
        'max_coupon_amount',
        'coupon',
        'amount_type',
        'coupon_type',
        'coupon_code',
        'limit',
        'start_date',
        'end_date',
        'rules',
        'total_used',
        'total_amount',
        'is_active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'min_trip_amount' => 'string',
        'max_coupon_amount' => 'string',
        'coupon' => 'string',
        'limit' => 'integer',
        'total_used' => 'float',
        'total_amount' => 'float',
        'is_active' => 'integer',
    ];

    public function categories()
    {
        return $this->belongsToMany(VehicleCategory::class)->using('Modules\PromotionManagement\Entities\CouponSetupVehicleCategory')->withTimestamps();
    }

    public function trips()
    {
        return $this->hasMany(TripRequest::class, 'coupon_id');
    }

    public function appliedCoupons()
    {
        return $this->hasMany(AppliedCoupon::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function level()
    {
        return $this->belongsTo(UserLevel::class, 'user_level_id');
    }

    public function logs()
    {
        return $this->morphMany(ActivityLog::class, 'logable');
    }

    public function getIsAppliedAttribute()
    {
        $user = User::where('id',auth('api')->id())->where('user_type',CUSTOMER)->first();
        return $user && $user->appliedCoupon && $user->appliedCoupon->coupon_setup_id == $this->id;
    }

    protected static function newFactory()
    {
        return \Modules\PromotionManagement\Database\factories\CouponSetupFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($item) {
            $array = [];
            foreach ($item->changes as $key => $change) {
                $array[$key] = $item->original[$key];
            }
            if (!empty($array)) {
                $log = new ActivityLog();
                $log->edited_by = auth()->user()->id ?? 'user_update';
                $log->before = $array;
                $log->after = $item->changes;
                $item->logs()->save($log);
            }
        });

        static::deleted(function ($item) {
            $log = new ActivityLog();
            $log->edited_by = auth()->user()->id;
            $log->before = $item->original;
            $item->logs()->save($log);
        });

    }
}

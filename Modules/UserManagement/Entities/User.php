<?php

namespace Modules\UserManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Modules\AdminModule\Entities\ActivityLog;
use Modules\AdminModule\Entities\AdminNotification;
use Modules\PromotionManagement\Entities\AppliedCoupon;
use Modules\ReviewModule\Entities\Review;
use Modules\TransactionManagement\Entities\Transaction;
use Modules\TripManagement\Entities\TripRequest;
use Modules\TripManagement\Entities\TripStatus;
use Modules\VehicleManagement\Entities\Vehicle;

class User extends Authenticatable
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes, HasApiTokens, HasFactory;

    protected $fillable = [
        'user_level_id',
        'full_name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'identification_number',
        'identification_type',
        'identification_image',
        'old_identification_image',
        'other_documents',
        'profile_image',
        'fcm_token',
        'phone_verified_at',
        'email_verified_at',
        'loyalty_points',
        'password',
        'user_type',
        'role_id',
        'remember_token',
        'is_active',
        'current_language_key',
        'failed_attempt',
        'is_temp_blocked',
        'blocked_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'identification_image' => 'array',
        'old_identification_image' => 'array',
        'other_documents' => 'array',
        'loyalty_points' => 'double'
    ];

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\UserFactory::new();
    }

    public function scopeUserType($query, $type)
    {
        return $query->where('user_type', $type);
    }

    public function scopeOfActive($query, $value = true)
    {
        return $query->where('is_active', $value);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id')->withTrashed();
    }

    public function level()
    {
        return $this->belongsTo(UserLevel::class, 'user_level_id');
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function moduleAccess()
    {
        return $this->hasMany(ModuleAccess::class, 'user_id');
    }

    public function lastLocations()
    {
        return $this->hasOne(UserLastLocation::class, 'user_id');
    }

    public function customerTripsStatus()
    {
        return $this->hasMany(TripStatus::class, 'customer_id');
    }

    public function driverTripsStatus()
    {
        return $this->hasMany(TripStatus::class, 'driver_id');
    }

    public function customerTrips()
    {
        return $this->hasMany(TripRequest::class, 'customer_id');
    }

    public function getCustomerPendingTrips()
    {
        return $this->customerTrips()
            ->where('current_status', PENDING)->get();
    }
    public function getCustomerAcceptedTrips()
    {
        return $this->customerTrips()
            ->where('current_status', ACCEPTED)->get();
    }

    public function getCustomerUnpaidParcelAndTrips()
    {
        return $this->customerTrips()
            ->whereNotNull(['driver_id'])
            ->where(function($query){
                $query->where(function($query1){
                    $query1->where('type','ride_request')
                        ->where('current_status', COMPLETED)
                        ->where('payment_status',UNPAID);
                })->orWhere(function($query2){
                    $query2->where('type','ride_request')
                        ->where('current_status', CANCELLED)
                        ->where('payment_status',UNPAID)
                        ->whereHas('fee', function ($query) {
                            $query->where('cancelled_by', 'customer');
                        });
                })->orWhere(function($query2){
                    $query2->where('type','parcel')
                        ->where('current_status', ONGOING)
                        ->where('payment_status',UNPAID);
                });
            })
            ->get();
    }

    public function getCustomerOngingTrips()
    {
        return $this->customerTrips()
            ->where('current_status', ONGOING)->get();
    }

    public function driverTrips()
    {
        return $this->hasMany(TripRequest::class, 'driver_id');
    }

    public function getDriverLastTrip()
    {
        return $this->driverTrips()
            ->whereIn('current_status', DV_DELETE_TRIP_CURRENT_STATUS)->get();
    }

    public function logs()
    {
        return $this->morphMany(ActivityLog::class, 'logable');
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'driver_id');
    }

    public function vehicleCategory()
    {
        return $this->hasOne(Vehicle::class, 'driver_id')->with('category');
    }

    public function userAccount()
    {
        return $this->hasOne(UserAccount::class, 'user_id');
    }

    public function givenReviews()
    {
        return $this->hasMany(Review::class, 'given_by');
    }

    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'received_by');
    }

    public function driverDetails()
    {
        return $this->hasOne(DriverDetail::class, 'user_id');
    }

    public function timeLog()
    {
        return $this->hasMany(DriverTimeLog::class, 'driver_id');
    }

    public function levelHistory()
    {
        return $this->hasMany(UserLevelHistory::class, 'user_id');
    }

    public function latestLevelHistory()
    {
        return $this->hasOne(UserLevelHistory::class, 'user_id')->latestOfMany();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function timeTrack()
    {
        return $this->hasMany(TimeTrack::class, 'user_id');
    }

    public function latestTrack()
    {
        return $this->hasOne(TimeTrack::class, 'user_id')->latestOfMany();
    }

    public function appliedCoupon()
    {
        return $this->hasOne(AppliedCoupon::class);
    }

    public function getCompletionPercentAttribute()
    {
        $attributes = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'identification_number',
            'identification_type',
            'identification_image',
            'other_documents',
            'date_of_birth',
            'profile_image'
        ];

        $complete = collect($attributes)
            ->map(fn($attribute) => $this->getAttribute($attribute))
            ->filter()
            ->count();
        return ($complete / count($attributes)) * 100;
    }

    public function isProfileVerified()
    {
        return $this?->first_name && $this?->last_name == null ? 0 : 1;
    }

    public function vehicleStatus()
    {
        // 0 = no_vehicle_added, 1 = vehicle_not_approved, 2 = vehicle_approved
        $vehicleStatus = 0;
        if ($this->vehicle) {
            $vehicleStatus = 1;
            if ($this->vehicle->is_active) {
                $vehicleStatus = 2;
            }
        }
        return $vehicleStatus;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            if (in_array($item->user_type, ['customer', 'driver'])) {
                $notification = new AdminNotification();
                $notification->model = 'user';
                $notification->model_id = $item->getAttributes()['id'];
                $notification->message = 'new_' . $item->user_type . '_registered';
                $notification->save();
            }
        });

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
                $log->user_type = $item->user_type;
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

<?php

namespace Modules\PromotionManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AdminModule\Entities\ActivityLog;
use Modules\PromotionManagement\Database\factories\DiscountSetupFactory;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\UserLevel;
use Modules\VehicleManagement\Entities\VehicleCategory;
use Modules\ZoneManagement\Entities\Zone;

class DiscountSetup extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'short_description',
        'terms_conditions',
        'image',
        'zone_discount_type',
        'customer_level_discount_type',
        'customer_discount_type',
        'module_discount_type',
        'discount_amount_type',
        'limit_per_user',
        'discount_amount',
        'max_discount_amount',
        'min_trip_amount',
        'start_date',
        'end_date',
        'total_used',
        'total_amount',
        'is_active',
    ];

    protected $casts = [
        'module_discount_type' => 'array',
        'discount_amount' => 'double',
        'max_discount_amount' => 'double',
        'min_trip_amount' => 'double',
        'start_date' => 'date',
        'end_date' => 'date',
        'limit_per_user' => 'integer',
        'total_used' => 'integer',
        'total_amount' => 'double',
        'is_active' => 'boolean',
    ];

    public function vehicleCategories()
    {
        return $this->belongsToMany(VehicleCategory::class, VehicleCategoryDiscountSetup::class);
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class, ZoneDiscountSetup::class);
    }

    public function customerLevels()
    {
        return $this->belongsToMany(UserLevel::class, CustomerLevelDiscountSetup::class);
    }

    public function customers()
    {
        return $this->belongsToMany(User::class, CustomerDiscountSetup::class);
    }

    public function getZoneDiscountAttribute()
    {
        if ($this->zone_discount_type === ALL) {
            $data[] = ALL;
            return $data;
        }
        $data = [];
        foreach ($this->zones->pluck('name')->toArray() as $zone) {
            $data[] = $zone;
        }
        return $data;
    }

    public function getCustomerLevelDiscountAttribute()
    {
        if ($this->customer_level_discount_type === ALL) {
            $data[] = ALL;
            return $data;
        }
        $data = [];
        foreach ($this->customerLevels->pluck('name')->toArray() as $customerLevel) {
            $data[] = $customerLevel;
        }
        return $data;
    }

    public function getCustomerDiscountAttribute()
    {
        if ($this->customer_discount_type === ALL) {
            $data[] = ALL;
            return $data;
        }
        $data = [];
        foreach ($this->customers as $customer) {
            $data[] = $customer->first_name . ' ' . $customer->last_name;
        }
        return $data;
    }

    public function getModuleDiscountAttribute()
    {
        if (in_array(ALL, $this->module_discount_type, true)) {
            $data[] = ALL;
            return $data;
        } elseif (in_array(PARCEL, $this->module_discount_type, true) && in_array(CUSTOM, $this->module_discount_type, true)) {
            $data[] = PARCEL;
            foreach ($this->vehicleCategories->pluck('name')->toArray() as $vehicleCategory) {
                $data[] = $vehicleCategory;
            }
            return $data;
        } elseif (in_array(PARCEL, $this->module_discount_type, true)) {
            $data[] = PARCEL;
            return $data;
        } elseif (in_array(CUSTOM, $this->module_discount_type, true)) {
            $data = [];
            foreach ($this->vehicleCategories->pluck('name')->toArray() as $vehicleCategory) {
                $data[] = $vehicleCategory;
            }
            return $data;
        } else {
            return [];
        }
    }
    public function logs()
    {
        return $this->morphMany(ActivityLog::class, 'logable');
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

<?php

namespace Modules\PromotionManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PromotionManagement\Database\factories\VehicleCategoryCouponSetupFactory;
use Modules\VehicleManagement\Entities\VehicleCategory;

class VehicleCategoryCouponSetup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['vehicle_category_id', 'discount_setup_id'];


    public function vehicleCategory()
    {
        return $this->belongsTo(VehicleCategory::class);
    }

    public function coupon()
    {
        return $this->belongsTo(CouponSetup::class);
    }

    protected static function newFactory(): VehicleCategoryCouponSetupFactory
    {
        //return VehicleCategoryCouponSetupFactory::new();
    }
}

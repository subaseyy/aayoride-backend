<?php

namespace Modules\PromotionManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PromotionManagement\Database\factories\VehicleCategoryDiscountSetupFactory;
use Modules\VehicleManagement\Entities\VehicleCategory;

class VehicleCategoryDiscountSetup extends Model
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

    public function discount()
    {
        return $this->belongsTo(DiscountSetup::class);
    }

    protected static function newFactory(): VehicleCategoryDiscountSetupFactory
    {
        //return VehicleCategoryDiscountSetupFactory::new();
    }
}

<?php

namespace Modules\PromotionManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PromotionManagement\Database\factories\ZoneCouponSetupFactory;
use Modules\ZoneManagement\Entities\Zone;

class ZoneCouponSetup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['id','zone_id', 'coupon_setup_id'];


    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function coupon()
    {
        return $this->belongsTo(CouponSetup::class);
    }

    protected static function newFactory(): ZoneCouponSetupFactory
    {
        //return ZoneCouponSetupFactory::new();
    }
}

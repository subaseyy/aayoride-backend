<?php

namespace Modules\PromotionManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Gateways\Traits\HasUuid;
use Modules\PromotionManagement\Database\factories\AppliedCouponFactory;
use Modules\UserManagement\Entities\User;

class AppliedCoupon extends Model
{
    use HasFactory,HasUuid;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = ['user_id', 'coupon_setup_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(CouponSetup::class);
    }
}

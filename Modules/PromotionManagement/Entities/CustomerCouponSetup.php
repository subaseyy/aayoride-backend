<?php

namespace Modules\PromotionManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PromotionManagement\Database\factories\CustomerCouponSetupFactory;
use Modules\UserManagement\Entities\User;

class CustomerCouponSetup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['id','user_id', 'discount_setup_id','limit_per_user'];

    protected $casts = [
        'limit_per_user' => 'integer',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(CouponSetup::class);
    }

    protected static function newFactory(): CustomerCouponSetupFactory
    {
        //return CustomerCouponSetupFactory::new();
    }
}

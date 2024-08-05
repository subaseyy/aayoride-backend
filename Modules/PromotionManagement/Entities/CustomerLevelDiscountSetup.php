<?php

namespace Modules\PromotionManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PromotionManagement\Database\factories\CustomerLevelDiscountSetupFactory;
use Modules\UserManagement\Entities\UserLevel;

class CustomerLevelDiscountSetup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_level_id', 'discount_setup_id'];


    public function userLevel()
    {
        return $this->belongsTo(UserLevel::class);
    }

    public function discount()
    {
        return $this->belongsTo(DiscountSetup::class);
    }

    protected static function newFactory(): CustomerLevelDiscountSetupFactory
    {
        //return CustomerLevelDiscountSetupFactory::new();
    }
}

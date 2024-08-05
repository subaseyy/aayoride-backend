<?php

namespace Modules\PromotionManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\PromotionManagement\Database\factories\CustomerDiscountSetupFactory;
use Modules\UserManagement\Entities\User;

class CustomerDiscountSetup extends Model
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

    public function discount()
    {
        return $this->belongsTo(DiscountSetup::class);
    }

    protected static function newFactory(): CustomerDiscountSetupFactory
    {
        //return CustomerDiscountSetupFactory::new();
    }
}

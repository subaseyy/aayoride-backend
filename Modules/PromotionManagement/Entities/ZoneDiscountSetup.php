<?php

namespace Modules\PromotionManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Gateways\Traits\HasUuid;
use Modules\PromotionManagement\Database\factories\ZoneDiscountSetupFactory;
use Modules\ZoneManagement\Entities\Zone;

class ZoneDiscountSetup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['id','zone_id', 'discount_setup_id'];


    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function discount()
    {
        return $this->belongsTo(DiscountSetup::class);
    }

    protected static function newFactory(): ZoneDiscountSetupFactory
    {
        //return ZoneDiscountSetupFactory::new();
    }
}

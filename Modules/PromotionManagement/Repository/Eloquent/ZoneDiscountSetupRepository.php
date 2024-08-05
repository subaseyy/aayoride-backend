<?php

namespace Modules\PromotionManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\PromotionManagement\Entities\AppliedCoupon;
use Modules\PromotionManagement\Entities\CouponSetup;
use Modules\PromotionManagement\Entities\DiscountSetup;
use Modules\PromotionManagement\Entities\ZoneDiscountSetup;
use Modules\PromotionManagement\Repository\AppliedCouponRepositoryInterface;
use Modules\PromotionManagement\Repository\CouponSetupRepositoryInterface;
use Modules\PromotionManagement\Repository\DiscountSetupRepositoryInterface;
use Modules\PromotionManagement\Repository\ZoneDiscountSetupRepositoryInterface;
use Modules\UserManagement\Entities\User;

class ZoneDiscountSetupRepository extends BaseRepository implements ZoneDiscountSetupRepositoryInterface
{
    public function __construct(ZoneDiscountSetup $model)
    {
        parent::__construct($model);
    }

}

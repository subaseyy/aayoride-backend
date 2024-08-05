<?php

namespace Modules\PromotionManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\PromotionManagement\Entities\AppliedCoupon;
use Modules\PromotionManagement\Entities\CouponSetup;
use Modules\PromotionManagement\Entities\DiscountSetup;
use Modules\PromotionManagement\Entities\VehicleCategoryDiscountSetup;
use Modules\PromotionManagement\Repository\AppliedCouponRepositoryInterface;
use Modules\PromotionManagement\Repository\CouponSetupRepositoryInterface;
use Modules\PromotionManagement\Repository\VehicleCategoryDiscountSetupRepositoryInterface;
use Modules\UserManagement\Entities\User;

class VehicleCategoryDiscountSetupRepository extends BaseRepository implements VehicleCategoryDiscountSetupRepositoryInterface
{
    public function __construct(VehicleCategoryDiscountSetup $model)
    {
        parent::__construct($model);
    }

}

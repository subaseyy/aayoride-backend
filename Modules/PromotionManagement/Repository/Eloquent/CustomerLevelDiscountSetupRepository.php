<?php

namespace Modules\PromotionManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\PromotionManagement\Entities\AppliedCoupon;
use Modules\PromotionManagement\Entities\CouponSetup;
use Modules\PromotionManagement\Entities\CustomerLevelDiscountSetup;
use Modules\PromotionManagement\Entities\DiscountSetup;
use Modules\PromotionManagement\Repository\AppliedCouponRepositoryInterface;
use Modules\PromotionManagement\Repository\CouponSetupRepositoryInterface;
use Modules\PromotionManagement\Repository\CustomerLevelDiscountSetupRepositoryInterface;
use Modules\UserManagement\Entities\User;

class CustomerLevelDiscountSetupRepository extends BaseRepository implements CustomerLevelDiscountSetupRepositoryInterface
{
    public function __construct(CustomerLevelDiscountSetup $model)
    {
        parent::__construct($model);
    }

}

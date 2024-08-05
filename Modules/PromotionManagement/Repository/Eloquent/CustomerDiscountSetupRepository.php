<?php

namespace Modules\PromotionManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\PromotionManagement\Entities\AppliedCoupon;
use Modules\PromotionManagement\Entities\CouponSetup;
use Modules\PromotionManagement\Entities\CustomerDiscountSetup;
use Modules\PromotionManagement\Entities\DiscountSetup;
use Modules\PromotionManagement\Repository\AppliedCouponRepositoryInterface;
use Modules\PromotionManagement\Repository\CouponSetupRepositoryInterface;
use Modules\PromotionManagement\Repository\CustomerDiscountSetupRepositoryInterface;
use Modules\UserManagement\Entities\User;

class CustomerDiscountSetupRepository extends BaseRepository implements CustomerDiscountSetupRepositoryInterface
{
    public function __construct(CustomerDiscountSetup $model)
    {
        parent::__construct($model);
    }

}

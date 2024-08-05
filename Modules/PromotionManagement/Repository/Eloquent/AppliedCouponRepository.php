<?php

namespace Modules\PromotionManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\PromotionManagement\Entities\AppliedCoupon;
use Modules\PromotionManagement\Entities\CouponSetup;
use Modules\PromotionManagement\Repository\AppliedCouponRepositoryInterface;
use Modules\PromotionManagement\Repository\CouponSetupRepositoryInterface;
use Modules\UserManagement\Entities\User;

class AppliedCouponRepository extends BaseRepository implements AppliedCouponRepositoryInterface
{
    public function __construct(AppliedCoupon $model)
    {
        parent::__construct($model);
    }

}

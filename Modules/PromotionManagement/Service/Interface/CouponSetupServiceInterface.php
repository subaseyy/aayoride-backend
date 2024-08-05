<?php

namespace Modules\PromotionManagement\Service\Interface;

use App\Service\BaseServiceInterface;

interface CouponSetupServiceInterface extends BaseServiceInterface
{
    public function getUserCouponList(array $data, $limit = null, $offset = null);

    public function getAppliedCoupon(array $data);

}

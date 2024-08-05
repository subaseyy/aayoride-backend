<?php

namespace Modules\PromotionManagement\Repository;

use App\Repository\EloquentRepositoryInterface;

interface CouponSetupRepositoryInterface extends EloquentRepositoryInterface
{
    public function fetchCouponDataCount($dateRange, string $status = null): int;

    public function getUserCouponList(array $data, $limit= null, $offset = null);

    public function getAppliedCoupon(array $data);
}

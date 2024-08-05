<?php

namespace Modules\PromotionManagement\Repository;

use App\Repository\EloquentRepositoryInterface;

interface DiscountSetupRepositoryInterface extends EloquentRepositoryInterface
{
    public function getUserDiscountList(array $data, $limit = null, $offset = null);
    public function getUserTripApplicableDiscountList($tripType, $vehicleCategoryId, array $data, $limit = null, $offset = null);

}

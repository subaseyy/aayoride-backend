<?php

namespace Modules\PromotionManagement\Service\Interface;

use App\Service\BaseServiceInterface;

interface DiscountSetupServiceInterface extends BaseServiceInterface
{
    public function getUserDiscountList(array $data, $limit = null, $offset = null);
    public function getUserTripApplicableDiscountList($tripType, $vehicleCategoryId, array $data, $limit = null, $offset = null);

}

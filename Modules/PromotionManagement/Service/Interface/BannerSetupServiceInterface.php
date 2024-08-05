<?php

namespace Modules\PromotionManagement\Service\Interface;

use App\Service\BaseServiceInterface;

interface BannerSetupServiceInterface extends BaseServiceInterface
{
    public function list($data,$limit,$offset);
}

<?php

namespace Modules\PromotionManagement\Repository;

use App\Repository\EloquentRepositoryInterface;

interface BannerSetupRepositoryInterface extends EloquentRepositoryInterface
{
    public function list($data,$limit,$offset);
}

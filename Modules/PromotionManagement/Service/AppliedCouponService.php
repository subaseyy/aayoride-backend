<?php

namespace Modules\PromotionManagement\Service;

use App\Service\BaseService;
use Illuminate\Database\Eloquent\Model;
use Modules\PromotionManagement\Repository\AppliedCouponRepositoryInterface;
use Modules\PromotionManagement\Repository\CouponSetupRepositoryInterface;
use Modules\PromotionManagement\Service\Interface\CouponSetupServiceInterface;
use Modules\TripManagement\Repository\TripRequestRepositoryInterface;

class AppliedCouponService extends BaseService implements Interface\AppliedCouponServiceInterface
{
    protected $appliedCouponRepository;

    public function __construct(AppliedCouponRepositoryInterface $appliedCouponRepository)
    {
        parent::__construct($appliedCouponRepository);
        $this->appliedCouponRepository = $appliedCouponRepository;

    }




}

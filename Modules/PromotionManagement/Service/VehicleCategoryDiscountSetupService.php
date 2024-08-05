<?php

namespace Modules\PromotionManagement\Service;

use App\Service\BaseService;
use Modules\PromotionManagement\Repository\VehicleCategoryDiscountSetupRepositoryInterface;

class VehicleCategoryDiscountSetupService extends BaseService implements Interface\VehicleCategoryDiscountSetupServiceInterface
{
    protected $vehicleCategoryDiscountSetupRepository;

    public function __construct(VehicleCategoryDiscountSetupRepositoryInterface $vehicleCategoryDiscountSetupRepository)
    {
        parent::__construct($vehicleCategoryDiscountSetupRepository);
        $this->vehicleCategoryDiscountSetupRepository = $vehicleCategoryDiscountSetupRepository;

    }




}

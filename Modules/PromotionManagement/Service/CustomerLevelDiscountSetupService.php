<?php

namespace Modules\PromotionManagement\Service;

use App\Service\BaseService;
use Modules\PromotionManagement\Repository\CustomerLevelDiscountSetupRepositoryInterface;

class CustomerLevelDiscountSetupService extends BaseService implements Interface\CustomerLevelDiscountSetupServiceInterface
{
    protected $customerLevelDiscountSetupRepository;

    public function __construct(CustomerLevelDiscountSetupRepositoryInterface $customerLevelDiscountSetupRepository)
    {
        parent::__construct($customerLevelDiscountSetupRepository);
        $this->customerLevelDiscountSetupRepository = $customerLevelDiscountSetupRepository;

    }




}

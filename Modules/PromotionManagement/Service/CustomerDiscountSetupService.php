<?php

namespace Modules\PromotionManagement\Service;

use App\Service\BaseService;
use Modules\PromotionManagement\Repository\CustomerDiscountSetupRepositoryInterface;

class CustomerDiscountSetupService extends BaseService implements Interface\CustomerDiscountSetupServiceInterface
{
    protected $customerDiscountSetupRepository;

    public function __construct(CustomerDiscountSetupRepositoryInterface $customerDiscountSetupRepository)
    {
        parent::__construct($customerDiscountSetupRepository);
        $this->customerDiscountSetupRepository = $customerDiscountSetupRepository;

    }




}

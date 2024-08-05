<?php

namespace Modules\PromotionManagement\Service;

use App\Service\BaseService;
use Modules\PromotionManagement\Repository\ZoneDiscountSetupRepositoryInterface;

class ZoneDiscountSetupService extends BaseService implements Interface\ZoneDiscountSetupServiceInterface
{
    protected $zoneDiscountSetupRepository;

    public function __construct(ZoneDiscountSetupRepositoryInterface $zoneDiscountSetupRepository)
    {
        parent::__construct($zoneDiscountSetupRepository);
        $this->zoneDiscountSetupRepository = $zoneDiscountSetupRepository;

    }




}

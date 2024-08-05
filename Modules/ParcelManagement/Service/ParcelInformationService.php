<?php

namespace Modules\ParcelManagement\Service;

use App\Service\BaseService;
use Modules\ParcelManagement\Repository\ParcelInformationRepositoryInterface;
use Modules\ParcelManagement\Service\Interface\ParcelInformationServiceInterface;

class ParcelInformationService extends BaseService implements ParcelInformationServiceInterface
{
    public function __construct(ParcelInformationRepositoryInterface $baseRepository)
    {
        parent::__construct($baseRepository);
    }
}

<?php

namespace Modules\ParcelManagement\Service;

use App\Service\BaseService;
use Modules\ParcelManagement\Repository\ParcelUserInformationRepositoryInterface;
use Modules\ParcelManagement\Service\Interface\ParcelUserInformationServiceInterface;

class ParcelUserInformationService extends BaseService implements ParcelUserInformationServiceInterface
{
    public function __construct(ParcelUserInformationRepositoryInterface $baseRepository)
    {
        parent::__construct($baseRepository);
    }
}

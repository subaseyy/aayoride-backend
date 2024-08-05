<?php

namespace Modules\FareManagement\Service;

use App\Service\BaseService;
use Modules\FareManagement\Repository\ParcelFareRepositoryInterface;

class ParcelFareService extends BaseService implements Interface\ParcelFareServiceInterface
{
    protected $parcelFareRepository;
    public function __construct(ParcelFareRepositoryInterface $parcelFareRepository)
    {
        parent::__construct($parcelFareRepository);
        $this->parcelFareRepository = $parcelFareRepository;
    }
}

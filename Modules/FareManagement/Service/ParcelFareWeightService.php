<?php

namespace Modules\FareManagement\Service;

use App\Service\BaseService;
use Modules\FareManagement\Repository\ParcelFareWeightRepositoryInterface;
use Modules\FareManagement\Service\Interface\ParcelFareWeightServiceInterface;

class ParcelFareWeightService extends BaseService implements ParcelFareWeightServiceInterface
{
    protected $parcelFareWeightRepository;
    public function __construct(ParcelFareWeightRepositoryInterface $parcelFareWeightRepository)
    {
        parent::__construct($parcelFareWeightRepository);
        $this->parcelFareWeightRepository = $parcelFareWeightRepository;
    }
}

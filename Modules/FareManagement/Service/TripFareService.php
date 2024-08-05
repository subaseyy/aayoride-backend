<?php

namespace Modules\FareManagement\Service;

use App\Service\BaseService;
use Modules\FareManagement\Repository\TripFareRepositoryInterface;
use Modules\FareManagement\Service\Interface\TripFareServiceInterface;

class TripFareService extends BaseService implements TripFareServiceInterface
{
    protected $TripFareRepository;
    public function __construct(TripFareRepositoryInterface $TripFareRepository)
    {
        parent::__construct($TripFareRepository);
        $this->TripFareRepository = $TripFareRepository;
    }
}

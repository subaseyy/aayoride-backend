<?php

namespace Modules\TripManagement\Service;


use App\Service\BaseService;
use Modules\TripManagement\Repository\TripRequestCoordinateRepositoryInterface;
use Modules\TripManagement\Service\Interface\TripRequestCoordinateServiceInterface;

class TripRequestCoordinateService extends BaseService implements TripRequestCoordinateServiceInterface
{
    protected $tripRequestCoordinateRepository;

    public function __construct(TripRequestCoordinateRepositoryInterface $tripRequestCoordinateRepository)
    {
        parent::__construct($tripRequestCoordinateRepository);
        $this->tripRequestCoordinateRepository = $tripRequestCoordinateRepository;
    }

    // Add your specific methods related to TripRequestCoordinateService here
}

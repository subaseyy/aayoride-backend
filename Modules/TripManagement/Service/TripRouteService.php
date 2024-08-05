<?php

namespace Modules\TripManagement\Service;


use App\Service\BaseService;
use Modules\TripManagement\Repository\TripRouteRepositoryInterface;
use Modules\TripManagement\Service\Interface\TripRouteServiceInterface;

class TripRouteService extends BaseService implements TripRouteServiceInterface
{
    protected $tripRouteRepository;

    public function __construct(TripRouteRepositoryInterface $tripRouteRepository)
    {
        parent::__construct($tripRouteRepository);
        $this->tripRouteRepository = $tripRouteRepository;
    }

    // Add your specific methods related to TripRouteService here
}

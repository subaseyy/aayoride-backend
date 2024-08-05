<?php

namespace Modules\TripManagement\Service;


use App\Service\BaseService;
use Modules\TripManagement\Repository\TripRequestTimeRepositoryInterface;
use Modules\TripManagement\Service\Interface\TripRequestTimeServiceInterface;

class TripRequestTimeService extends BaseService implements TripRequestTimeServiceInterface
{
    protected $tripRequestTimeRepository;

    public function __construct(TripRequestTimeRepositoryInterface $tripRequestTimeRepository)
    {
        parent::__construct($tripRequestTimeRepository);
        $this->tripRequestTimeRepository = $tripRequestTimeRepository;
    }

    // Add your specific methods related to TripRequestTimeService here
}

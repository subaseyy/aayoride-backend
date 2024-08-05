<?php

namespace Modules\TripManagement\Service;


use App\Service\BaseService;
use Modules\TripManagement\Repository\TripStatusRepositoryInterface;
use Modules\TripManagement\Service\Interface\TripStatusServiceInterface;

class TripStatusService extends BaseService implements TripStatusServiceInterface
{
    protected $tripStatusRepository;

    public function __construct(TripStatusRepositoryInterface $tripStatusRepository)
    {
        parent::__construct($tripStatusRepository);
        $this->tripStatusRepository = $tripStatusRepository;
    }

    // Add your specific methods related to TripStatusService here
}

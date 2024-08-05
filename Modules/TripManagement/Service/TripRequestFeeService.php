<?php

namespace Modules\TripManagement\Service;


use App\Service\BaseService;
use Modules\TripManagement\Repository\TripRequestFeeRepositoryInterface;
use Modules\TripManagement\Service\Interface\TripRequestFeeServiceInterface;

class TripRequestFeeService extends BaseService implements TripRequestFeeServiceInterface
{
    protected $tripRequestFeeRepository;

    public function __construct(TripRequestFeeRepositoryInterface $tripRequestFeeRepository)
    {
        parent::__construct($tripRequestFeeRepository);
        $this->tripRequestFeeRepository = $tripRequestFeeRepository;
    }

    // Add your specific methods related to TripRequestFeeService here
}

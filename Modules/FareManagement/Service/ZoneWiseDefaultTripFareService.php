<?php

namespace Modules\FareManagement\Service;

use App\Service\BaseService;
use Modules\FareManagement\Repository\Eloquent\ZoneWiseDefaultTripFareRepository;
use Modules\FareManagement\Service\Interface\ZoneWiseDefaultTripFareServiceInterface;

class ZoneWiseDefaultTripFareService extends BaseService implements ZoneWiseDefaultTripFareServiceInterface
{
    protected $zoneWiseDefaultTripFareRepository;
    public function __construct(ZoneWiseDefaultTripFareRepository $zoneWiseDefaultTripFareRepository){
        parent::__construct($zoneWiseDefaultTripFareRepository);
        $this->zoneWiseDefaultTripFareRepository = $zoneWiseDefaultTripFareRepository;
    }
}

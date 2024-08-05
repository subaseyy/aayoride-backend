<?php

namespace Modules\TripManagement\Service;


use App\Service\BaseService;
use Modules\TripManagement\Repository\RecentAddressRepositoryInterface;
use Modules\TripManagement\Service\Interface\RecentAddressServiceInterface;

class RecentAddressService extends BaseService implements RecentAddressServiceInterface
{
    protected $recentAddressRepository;

    public function __construct(RecentAddressRepositoryInterface $recentAddressRepository)
    {
        parent::__construct($recentAddressRepository);
        $this->recentAddressRepository = $recentAddressRepository;
    }

    // Add your specific methods related to RecentAddressService here
}

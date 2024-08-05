<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\UserLastLocationRepositoryInterface;
use Modules\UserManagement\Service\Interface\UserLastLocationServiceInterface;

class UserLastLocationService extends BaseService implements UserLastLocationServiceInterface
{
    protected $userLastLocationRepository;

    public function __construct(UserLastLocationRepositoryInterface $userLastLocationRepository)
    {
        parent::__construct($userLastLocationRepository);
        $this->userLastLocationRepository = $userLastLocationRepository;
    }

    // Add your specific methods related to UserLastLocationService here
}

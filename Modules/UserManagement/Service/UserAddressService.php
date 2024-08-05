<?php

namespace Modules\UserManagement\Service;

use App\Service\BaseService;
use Modules\UserManagement\Repository\UserAddressRepositoryInterface;
use Modules\UserManagement\Service\Interface\UserAddressServiceInterface;

class UserAddressService extends BaseService implements UserAddressServiceInterface
{
    protected $userAddressRepository;

    public function __construct(UserAddressRepositoryInterface $userAddressRepository)
    {
        parent::__construct($userAddressRepository);
        $this->userAddressRepository = $userAddressRepository;
    }

    // Add your specific methods related to UserAddressService here
}

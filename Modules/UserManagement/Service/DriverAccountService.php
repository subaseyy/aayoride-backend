<?php

namespace Modules\UserManagement\Service;

use App\Service\BaseService;
use Modules\UserManagement\Repository\UserAccountRepositoryInterface;
use Modules\UserManagement\Service\Interface\DriverAccountServiceInterface;

class DriverAccountService extends BaseService implements Interface\DriverAccountServiceInterface
{
    protected $userAccountRepository;

    public function __construct(UserAccountRepositoryInterface $userAccountRepository)
    {
        parent::__construct($userAccountRepository);
        $this->userAccountRepository = $userAccountRepository;
    }
}

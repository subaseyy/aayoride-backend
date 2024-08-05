<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\UserAccountRepositoryInterface;
use Modules\UserManagement\Service\Interface\UserAccountServiceInterface;

class UserAccountService extends BaseService implements UserAccountServiceInterface
{
    protected $userAccountRepository;

    public function __construct(UserAccountRepositoryInterface $userAccountRepository)
    {
        parent::__construct($userAccountRepository);
        $this->userAccountRepository = $userAccountRepository;
    }

    // Add your specific methods related to UserAccountService here
}

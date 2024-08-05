<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\UserLevelRepositoryInterface;
use Modules\UserManagement\Service\Interface\UserLevelServiceInterface;

class UserLevelService extends BaseService implements UserLevelServiceInterface
{
    protected $userLevelRepository;

    public function __construct(UserLevelRepositoryInterface $userLevelRepository)
    {
        parent::__construct($userLevelRepository);
        $this->userLevelRepository = $userLevelRepository;
    }

    // Add your specific methods related to UserLevelService here
}

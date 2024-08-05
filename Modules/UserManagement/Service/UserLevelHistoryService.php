<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\UserLevelHistoryRepositoryInterface;
use Modules\UserManagement\Service\Interface\UserLevelHistoryServiceInterface;

class UserLevelHistoryService extends BaseService implements UserLevelHistoryServiceInterface
{
    protected $userLevelHistoryRepository;

    public function __construct(UserLevelHistoryRepositoryInterface $userLevelHistoryRepository)
    {
        parent::__construct($userLevelHistoryRepository);
        $this->userLevelHistoryRepository = $userLevelHistoryRepository;
    }

    // Add your specific methods related to UserLevelHistoryService here
}

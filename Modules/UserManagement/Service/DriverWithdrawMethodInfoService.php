<?php

namespace Modules\UserManagement\Service;

use App\Service\BaseService;
use Modules\UserManagement\Repository\UserWithdrawMethodInfoRepositoryInterface;
use Modules\UserManagement\Service\Interface\DriverWithdrawMethodInfoServiceInterface;

class DriverWithdrawMethodInfoService extends BaseService implements Interface\DriverWithdrawMethodInfoServiceInterface
{
    protected $userWithdrawMethodInfoRepository;

    public function __construct(UserWithdrawMethodInfoRepositoryInterface $userWithdrawMethodInfoRepository)
    {
        parent::__construct($userWithdrawMethodInfoRepository);
        $this->userWithdrawMethodInfoRepository = $userWithdrawMethodInfoRepository;
    }
}

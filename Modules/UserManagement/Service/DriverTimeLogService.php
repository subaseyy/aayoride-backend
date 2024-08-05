<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\DriverTimeLogRepositoryInterface;
use Modules\UserManagement\Service\Interface\DriverTimeLogServiceInterface;

class DriverTimeLogService extends BaseService implements DriverTimeLogServiceInterface
{
    protected $driverTimeLogRepository;

    public function __construct(DriverTimeLogRepositoryInterface $driverTimeLogRepository)
    {
        parent::__construct($driverTimeLogRepository);
        $this->driverTimeLogRepository = $driverTimeLogRepository;
    }

    // Add your specific methods related to DriverTimeLogService here
}

<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\TimeLogRepositoryInterface;
use Modules\UserManagement\Service\Interface\TimeLogServiceInterface;

class TimeLogService extends BaseService implements TimeLogServiceInterface
{
    protected $timeLogRepository;

    public function __construct(TimeLogRepositoryInterface $timeLogRepository)
    {
        parent::__construct($timeLogRepository);
        $this->timeLogRepository = $timeLogRepository;
    }

    // Add your specific methods related to TimeLogService here
}

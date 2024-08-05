<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\TimeTrackRepositoryInterface;
use Modules\UserManagement\Service\Interface\TimeTrackServiceInterface;

class TimeTrackService extends BaseService implements TimeTrackServiceInterface
{
    protected $timeTrackRepository;

    public function __construct(TimeTrackRepositoryInterface $timeTrackRepository)
    {
        parent::__construct($timeTrackRepository);
        $this->timeTrackRepository = $timeTrackRepository;
    }

    // Add your specific methods related to TimeTrackService here
}

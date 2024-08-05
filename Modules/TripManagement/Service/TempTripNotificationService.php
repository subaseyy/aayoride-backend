<?php

namespace Modules\TripManagement\Service;


use App\Service\BaseService;
use Modules\TripManagement\Repository\TempTripNotificationRepositoryInterface;
use Modules\TripManagement\Service\Interface\TempTripNotificationServiceInterface;

class TempTripNotificationService extends BaseService implements TempTripNotificationServiceInterface
{
    protected $tempTripNotificationRepository;

    public function __construct(TempTripNotificationRepositoryInterface $tempTripNotificationRepository)
    {
        parent::__construct($tempTripNotificationRepository);
        $this->tempTripNotificationRepository = $tempTripNotificationRepository;
    }

    // Add your specific methods related to TempTripNotificationService here
}

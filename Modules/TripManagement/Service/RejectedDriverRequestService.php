<?php

namespace Modules\TripManagement\Service;


use App\Service\BaseService;
use Modules\TripManagement\Repository\RejectedDriverRequestRepositoryInterface;
use Modules\TripManagement\Service\Interface\RejectedDriverRequestServiceInterface;

class RejectedDriverRequestService extends BaseService implements RejectedDriverRequestServiceInterface
{
    protected $rejectedDriverRequestRepository;

    public function __construct(RejectedDriverRequestRepositoryInterface $rejectedDriverRequestRepository)
    {
        parent::__construct($rejectedDriverRequestRepository);
        $this->rejectedDriverRequestRepository = $rejectedDriverRequestRepository;
    }

    // Add your specific methods related to RejectedDriverRequestService here
}

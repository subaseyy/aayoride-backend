<?php

namespace Modules\TripManagement\Service;


use App\Service\BaseService;
use Modules\TripManagement\Repository\FareBiddingLogRepositoryInterface;
use Modules\TripManagement\Service\Interface\FareBiddingLogServiceInterface;

class FareBiddingLogService extends BaseService implements FareBiddingLogServiceInterface
{
    protected $fareBiddingLogRepository;

    public function __construct(FareBiddingLogRepositoryInterface $fareBiddingLogRepository)
    {
        parent::__construct($fareBiddingLogRepository);
        $this->fareBiddingLogRepository = $fareBiddingLogRepository;
    }



    // Add your specific methods related to FareBiddingLogService here
}

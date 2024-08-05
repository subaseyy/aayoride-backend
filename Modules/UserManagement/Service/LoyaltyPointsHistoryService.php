<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\LoyaltyPointsHistoryRepositoryInterface;
use Modules\UserManagement\Service\Interface\LoyaltyPointsHistoryServiceInterface;

class LoyaltyPointsHistoryService extends BaseService implements LoyaltyPointsHistoryServiceInterface
{
    protected $loyaltyPointsHistoryRepository;

    public function __construct(LoyaltyPointsHistoryRepositoryInterface $loyaltyPointsHistoryRepository)
    {
        parent::__construct($loyaltyPointsHistoryRepository);
        $this->loyaltyPointsHistoryRepository = $loyaltyPointsHistoryRepository;
    }

    // Add your specific methods related to LoyaltyPointsHistoryService here
}

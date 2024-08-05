<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\LoyaltyPointsHistory;
use Modules\UserManagement\Repository\LoyaltyPointsHistoryRepositoryInterface;

class LoyaltyPointsHistoryRepository extends BaseRepository implements LoyaltyPointsHistoryRepositoryInterface
{
    public function __construct(LoyaltyPointsHistory $model)
    {
        parent::__construct($model);
    }
}

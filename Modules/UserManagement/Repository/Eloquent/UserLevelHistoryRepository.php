<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\UserLevelHistory;
use Modules\UserManagement\Repository\UserLevelHistoryRepositoryInterface;

class UserLevelHistoryRepository extends BaseRepository implements UserLevelHistoryRepositoryInterface
{
    public function __construct(UserLevelHistory $model)
    {
        parent::__construct($model);
    }
}

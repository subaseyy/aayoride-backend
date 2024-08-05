<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\UserManagement\Entities\UserWithdrawMethodInfo;
use Modules\UserManagement\Repository\UserWithdrawMethodInfoRepositoryInterface;

class UserWithdrawMethodInfoRepository extends BaseRepository implements UserWithdrawMethodInfoRepositoryInterface
{
    public function __construct(UserWithdrawMethodInfo $model){
        parent::__construct($model);
    }
}

<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\WithdrawRequest;
use Modules\UserManagement\Repository\WithdrawRequestRepositoryInterface;

class WithdrawRequestRepository extends BaseRepository implements WithdrawRequestRepositoryInterface
{
    public function __construct(WithdrawRequest $model)
    {
        parent::__construct($model);
    }
}

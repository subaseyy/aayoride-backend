<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\WithdrawMethod;
use Modules\UserManagement\Repository\WithdrawMethodRepositoryInterface;

class WithdrawMethodRepository extends BaseRepository implements WithdrawMethodRepositoryInterface
{
    public function __construct(WithdrawMethod $model)
    {
        parent::__construct($model);
    }
}

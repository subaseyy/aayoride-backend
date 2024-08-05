<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\UserAccount;
use Modules\UserManagement\Repository\UserAccountRepositoryInterface;

class UserAccountRepository extends BaseRepository implements UserAccountRepositoryInterface
{
    public function __construct(UserAccount $model)
    {
        parent::__construct($model);
    }

    public function updateManyWithIncrement(array $ids, $column, $amount = 0)
    {
        $this->model->whereIn('id', $ids)->increment($column, $amount);
    }
}

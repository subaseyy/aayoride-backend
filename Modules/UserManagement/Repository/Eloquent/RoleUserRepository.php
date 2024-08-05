<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\RoleUser;
use Modules\UserManagement\Repository\RoleUserRepositoryInterface;

class RoleUserRepository extends BaseRepository implements RoleUserRepositoryInterface
{
    public function __construct(RoleUser $model)
    {
        parent::__construct($model);
    }
}

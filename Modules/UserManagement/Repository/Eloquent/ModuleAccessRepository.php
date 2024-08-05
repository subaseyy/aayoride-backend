<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\ModuleAccess;
use Modules\UserManagement\Repository\ModuleAccessRepositoryInterface;

class ModuleAccessRepository extends BaseRepository implements ModuleAccessRepositoryInterface
{
    public function __construct(ModuleAccess $model)
    {
        parent::__construct($model);
    }
}

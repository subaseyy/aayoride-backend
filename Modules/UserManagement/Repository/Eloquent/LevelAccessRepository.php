<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\LevelAccess;
use Modules\UserManagement\Repository\LevelAccessRepositoryInterface;

class LevelAccessRepository extends BaseRepository implements LevelAccessRepositoryInterface
{
    public function __construct(LevelAccess $model)
    {
        parent::__construct($model);
    }
}

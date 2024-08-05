<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\TimeTrack;
use Modules\UserManagement\Repository\TimeTrackRepositoryInterface;

class TimeTrackRepository extends BaseRepository implements TimeTrackRepositoryInterface
{
    public function __construct(TimeTrack $model)
    {
        parent::__construct($model);
    }
}

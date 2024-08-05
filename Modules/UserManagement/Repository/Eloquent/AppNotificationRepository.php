<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\AppNotification;
use Modules\UserManagement\Repository\AppNotificationRepositoryInterface;

class AppNotificationRepository extends BaseRepository implements AppNotificationRepositoryInterface
{
    public function __construct(AppNotification $model)
    {
        parent::__construct($model);
    }
}

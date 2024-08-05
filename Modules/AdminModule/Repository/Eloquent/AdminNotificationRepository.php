<?php

namespace Modules\AdminModule\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\AdminModule\Entities\AdminNotification;
use Modules\AdminModule\Repository\AdminNotificationRepositoryInterface;

class AdminNotificationRepository extends BaseRepository implements AdminNotificationRepositoryInterface
{
    public function __construct(AdminNotification $model)
    {
        parent::__construct($model);
    }
}

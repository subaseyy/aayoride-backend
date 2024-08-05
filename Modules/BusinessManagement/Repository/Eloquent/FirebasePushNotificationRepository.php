<?php

namespace Modules\BusinessManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\BusinessManagement\Entities\FirebasePushNotification;
use Modules\BusinessManagement\Repository\FirebasePushNotificationRepositoryInterface;

class FirebasePushNotificationRepository extends BaseRepository implements FirebasePushNotificationRepositoryInterface
{
    public function __construct(FirebasePushNotification $model)
    {
        parent::__construct($model);
    }
}

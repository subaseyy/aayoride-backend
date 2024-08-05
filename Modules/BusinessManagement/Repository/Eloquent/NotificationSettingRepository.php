<?php

namespace Modules\BusinessManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\BusinessManagement\Entities\NotificationSetting;
use Modules\BusinessManagement\Repository\NotificationSettingRepositoryInterface;

class NotificationSettingRepository extends BaseRepository implements NotificationSettingRepositoryInterface
{
    public function __construct(NotificationSetting $model)
    {
        parent::__construct($model);
    }
}

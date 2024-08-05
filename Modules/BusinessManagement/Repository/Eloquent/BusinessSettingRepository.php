<?php

namespace Modules\BusinessManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\BusinessManagement\Entities\BusinessSetting;
use Modules\BusinessManagement\Repository\BusinessSettingRepositoryInterface;

class BusinessSettingRepository extends BaseRepository implements BusinessSettingRepositoryInterface
{
    public function __construct(BusinessSetting $model)
    {
        parent::__construct($model);
    }
}

<?php

namespace Modules\BusinessManagement\Service\Interface;

use App\Service\BaseServiceInterface;

interface SettingServiceInterface extends BaseServiceInterface
{
    public function storeOrUpdatePaymentSetting(array $data);

    public function storeOrUpdateSMSSetting(array $data);
}

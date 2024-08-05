<?php

namespace Modules\BusinessManagement\Service\Interface;

use App\Service\BaseServiceInterface;
use Illuminate\Database\Eloquent\Model;

interface BusinessSettingServiceInterface extends BaseServiceInterface
{

    public function storeBusinessInfo(array $data);

    public function updateSetting(array $data);

    public function maintenance(array $data): ?Model;

    public function storeDriverSetting(array $data);

    public function storeCustomerSetting(array $data);

    public function storeTripFareSetting(array $data);

    public function storeBusinessPage(array $data);
    public function storeLandingPageIntroSection(array $data);

    public function storeLandingPageOurSolutionsSection(array $data);
    public function storeLandingPageOurSolutionsData(array $data);

    public function storeLandingPageBusinessStatistics(array $data);
    public function storeLandingPageEarnMoney(array $data);
    public function storeLandingPageTestimonial(array $data);
    public function storeLandingPageCTA(array $data);

    public function storeEmailConfig(array $data);
    public function storeGoogleMapApi(array $data);
    public function storeRecaptha(array $data);
}

<?php

namespace Modules\BusinessManagement\Service\Interface;

use App\Service\BaseServiceInterface;

interface LanguageSettingServiceInterface extends BaseServiceInterface
{
    public function storeLanguage(array $data);
    public function updateLanguage(array $data);
    public function deleteLanguage($lang);
    public function changeLanguageStatus(array $data);
    public function changeLanguageDefaultStatus(array $data);

    public function translate($lang);
    public function storeTranslate(array $data, $lang);
    public function autoTranslate(array $data, $lang);
}

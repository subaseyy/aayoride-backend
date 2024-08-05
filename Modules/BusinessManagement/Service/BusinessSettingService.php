<?php

namespace Modules\BusinessManagement\Service;

use App\Service\BaseService;
use App\Traits\UnloadedHelpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Modules\BusinessManagement\Repository\BusinessSettingRepositoryInterface;
use Modules\BusinessManagement\Service\Interface\BusinessSettingServiceInterface;

class BusinessSettingService extends BaseService implements BusinessSettingServiceInterface
{
    protected $businessSettingRepository;

    public function __construct(BusinessSettingRepositoryInterface $businessSettingRepository)
    {
        parent::__construct($businessSettingRepository);
        $this->businessSettingRepository = $businessSettingRepository;
    }

    public function storeBusinessInfo(array $data)
    {
        $code = 'USD';
        $symbol = "$";

        if (array_key_exists('currency_code', $data)) {
            $code = $data['currency_code'];
        }
        foreach (CURRENCIES as $currency) {
            if ($currency['code'] == $code) {
                $symbol = $currency['symbol'];
                break;
            }
        }

        $data['currency_code'] = $code;
        $data['currency_symbol'] = $symbol;
        $session_keys = [
            'currency_decimal_point', 'currency_symbol_position', 'currency_code', 'header_logo', 'favicon',
            'preloader', 'copyright_text', 'time_format', 'currency_symbol', 'business_name'
        ];

        foreach ($data as $key => $value) {
            $businessInfo = $this->businessSettingRepository
                ->findOneBy(criteria: ['key_name' => $key, 'settings_type' => BUSINESS_INFORMATION]);
            $images = ['header_logo', 'favicon', 'preloader'];
            if (in_array($key, $images)) {
                $value = fileUploader('business/', 'png', $data[$key], $businessInfo->value ?? '');
            }

            $value == 'on' ? $value = 1 : null;

            if ($value) {
                if ($businessInfo) {
                    $this->businessSettingRepository->update(id: $businessInfo->id, data: [
                        'key_name' => $key,
                        'value' => $value,
                        'settings_type' => BUSINESS_INFORMATION
                    ]);
                } else {
                    $this->businessSettingRepository->create(data: [
                        'key_name' => $key,
                        'value' => $value,
                        'settings_type' => BUSINESS_INFORMATION
                    ]);
                }

            }

            if (in_array($key, $session_keys)) {
                Session::put($key, $value);
            }

        }
        $absent_keys = ['driver_verification', 'customer_verification', 'email_verification', 'driver_self_registration', 'otp_verification'];
        foreach ($absent_keys as $absent) {
            if (!array_key_exists($absent, $data)) {
                $businessInfo = $this->businessSettingRepository
                    ->findOneBy(criteria: ['key_name' => $absent, 'settings_type' => 'business_information']);
                if ($businessInfo) {
                    $this->businessSettingRepository->update(id: $businessInfo->id, data: [
                        'key_name' => $absent,
                        'value' => 0,
                        'settings_type' => BUSINESS_INFORMATION
                    ]);
                } else {
                    $this->businessSettingRepository->create(data: [
                        'key_name' => $absent,
                        'value' => 0,
                        'settings_type' => BUSINESS_INFORMATION
                    ]);
                }
            }
        }
    }

    public function updateSetting(array $data)
    {
        if (array_key_exists('websocket_url',$data)){
            UnloadedHelpers::setEnvironmentValue('PUSHER_HOST',getMainDomain(url('/')));
            UnloadedHelpers::setEnvironmentValue('REVERB_HOST',getMainDomain(url('/')));
            $data['websocket_url'] = getMainDomain(url('/'));

        }
        if (array_key_exists('websocket_port',$data)){
            UnloadedHelpers::setEnvironmentValue('PUSHER_PORT',(int)$data['websocket_port']);
            UnloadedHelpers::setEnvironmentValue('REVERB_PORT',(int)$data['websocket_port']);
        }
        if (array_key_exists('bid_on_fare', $data)) {
            $data['bid_on_fare'] = 1;
        } else {
            $data['bid_on_fare'] = 0;
        }
        $not_cached = ['maximum_login_hit', 'temporary_login_block_time', 'maximum_otp_hit', 'otp_resend_time', 'temporary_block_time', 'pagination_limit'];
        foreach ($data as $key => $value) {
            if (!is_null($value)) {
                $businessSetting = $this->businessSettingRepository
                    ->findOneBy(criteria: ['key_name' => $key, 'settings_type' => BUSINESS_SETTINGS]);
                if ($businessSetting) {
                    $this->businessSettingRepository->update(id: $businessSetting->id, data: [
                        'key_name' => $key,
                        'value' => $value,
                        'settings_type' => BUSINESS_SETTINGS
                    ]);
                } else {
                    $this->businessSettingRepository->create(data: [
                        'key_name' => $key,
                        'value' => $value,
                        'settings_type' => BUSINESS_SETTINGS
                    ]);
                }

            }
            if (!in_array($key, $not_cached)) {
                //putting values on cache
                Cache::put($key, $value);
            }
            if ($key == 'pagination_limit') {
                Session::put('pagination_limit', $value ?? 10);
            }
        }
    }

    public function maintenance(array $data): ?Model
    {
        $maintenanceMode = $this->businessSettingRepository
            ->findOneBy(criteria: ['key_name' => 'maintenance_mode', 'settings_type' => BUSINESS_INFORMATION]);
        if ($maintenanceMode) {
            $maintenanceModeData = $this->businessSettingRepository->update(id: $maintenanceMode->id, data: [
                'key_name' => 'maintenance_mode',
                'value' => $data['status'],
                'settings_type' => BUSINESS_INFORMATION
            ]);
        } else {
            $maintenanceModeData = $this->businessSettingRepository->create(data: [
                'key_name' => 'maintenance_mode',
                'value' => $data['status'],
                'settings_type' => BUSINESS_INFORMATION
            ]);
        }

        return $maintenanceModeData;
    }

    public function storeDriverSetting(array $data)
    {
        if ($data['type'] == 'loyalty_point') {

            $storeData['type'] = 'loyalty_point';
            $storeData['loyalty_points'] = [
                'status' => ($data['loyalty_points']['status'] ?? 0) == 'on' ? 1 : 0,
                'points' => $data['loyalty_points']['value'] ?? 0,
            ];
        }
        foreach ($storeData as $key => $value) {
            $driverSetting = $this->businessSettingRepository
                ->findOneBy(criteria: ['key_name' => $key, 'settings_type' => DRIVER_SETTINGS]);
            if ($driverSetting) {
                $this->businessSettingRepository
                    ->update(id: $driverSetting->id, data: ['key_name' => $key, 'settings_type' => DRIVER_SETTINGS, 'value' => $value]);
            } else {
                $this->businessSettingRepository
                    ->create(data: ['key_name' => $key, 'settings_type' => DRIVER_SETTINGS, 'value' => $value]);
            }

        }
    }

    public function storeCustomerSetting(array $data)
    {
        if ($data['type'] == 'loyalty_point') {

            $storeData['type'] = 'loyalty_point';
            $storeData['loyalty_points'] = [
                'status' => ($data['loyalty_points']['status'] ?? 0) == 'on' ? 1 : 0,
                'points' => $data['loyalty_points']['value'] ?? 0,
            ];
        }
        foreach ($storeData as $key => $value) {
            $driverSetting = $this->businessSettingRepository
                ->findOneBy(criteria: ['key_name' => $key, 'settings_type' => CUSTOMER_SETTINGS]);
            if ($driverSetting) {
                $this->businessSettingRepository
                    ->update(id: $driverSetting->id, data: ['key_name' => $key, 'settings_type' => CUSTOMER_SETTINGS, 'value' => $value]);
            } else {
                $this->businessSettingRepository
                    ->create(data: ['key_name' => $key, 'settings_type' => 'customer_settings', 'value' => $value]);
            }

        }
    }

    public function storeTripFareSetting(array $data)
    {
        if ($data['type'] == 'trip_settings') {
            if (!array_key_exists('bidding_push_notification', $data)) {
                $data['bidding_push_notification'] = 0;
            }
            if (!array_key_exists('trip_push_notification', $data)) {
                $data['trip_push_notification'] = 0;
            }
        }
        foreach ($data as $key => $value) {
            $driverSetting = $this->businessSettingRepository
                ->findOneBy(criteria: ['key_name' => $key, 'settings_type' => TRIP_SETTINGS]);
            if ($driverSetting) {
                $this->businessSettingRepository
                    ->update(id: $driverSetting->id, data: ['key_name' => $key, 'settings_type' => TRIP_SETTINGS, 'value' => $value]);
            } else {
                $this->businessSettingRepository
                    ->create(data: ['key_name' => $key, 'settings_type' => TRIP_SETTINGS, 'value' => $value]);
            }
            Cache::put($key, $value);
        }
    }

    public function storeBusinessPage(array $data)
    {
        $value = [];
        $page = $this->businessSettingRepository->findOneBy(criteria: [
            'key_name' => $data['type'],
            'settings_type' => PAGES_SETTINGS
        ]);
        if (array_key_exists('image', $data)) {
            $fileName = fileUploader('business/pages/', 'png', $data['image'], $page->value['image'] ?? '');
            $value['image'] = $fileName;
        } else {
            $value['image'] = $page->value['image'] ?? '';
        }

        $value['name'] = $data['type'];
        $value['short_description'] = $data['short_description'];
        $value['long_description'] = $data['long_description'];
        if ($page) {
            $this->businessSettingRepository->update(id: $page->id, data: ['key_name' => $data['type'], 'settings_type' => PAGES_SETTINGS, 'value' => $value]);
        } else {
            $this->businessSettingRepository
                ->create(data: ['key_name' => $data['type'], 'settings_type' => PAGES_SETTINGS, 'value' => $value]);
        }
    }

    public function storeLandingPageIntroSection(array $data)
    {
        $introSection = $this->businessSettingRepository->findOneBy(criteria: [
            'key_name' => $data['type'],
            'settings_type' => LANDING_PAGES_SETTINGS
        ]);
        if ($data['type'] === INTRO_SECTION) {
            $value = ['title' => $data['title'], 'sub_title' => $data['sub_title']];
        }
        if ($data['type'] === INTRO_SECTION_IMAGE) {
            if (array_key_exists('background_image', $data)) {
                $fileName = fileUploader('business/landing-pages/intro-section/', $data['background_image']->extension(), $data['background_image'], $introSection->value['background_image'] ?? '');
                $value['background_image'] = $fileName;
            } else {
                $value['background_image'] = $introSection->value['background_image'] ?? '';
            }
        }
        if ($introSection) {
            $this->businessSettingRepository->update(id: $introSection->id, data: ['key_name' => $data['type'], 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
        } else {
            $this->businessSettingRepository->create(data: ['key_name' => $data['type'], 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
        }
    }

    public function storeLandingPageOurSolutionsSection(array $data): void
    {
        $ourSolutionsSection = $this->businessSettingRepository->findOneBy(criteria: [
            'key_name' => $data['type'],
            'settings_type' => LANDING_PAGES_SETTINGS
        ]);
        if ($data['type'] === OUR_SOLUTIONS_SECTION) {
            $value = ['title' => $data['title'], 'sub_title' => $data['sub_title']];
        }
        if ($data['type'] === OUR_SOLUTIONS_DATA) {
            if (array_key_exists('background_image', $data)) {
                $fileName = fileUploader('business/landing-pages/intro-section/', $data['background_image']->extension(), $data['background_image'], $ourSolutionsSection->value['background_image'] ?? '');
                $value['background_image'] = $fileName;
            } else {
                $value['background_image'] = $ourSolutionsSection->value['background_image'] ?? '';
            }
        }

        if ($ourSolutionsSection) {
            $this->businessSettingRepository->update(id: $ourSolutionsSection['id'], data: ['key_name' => $data['type'], 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
        } else {
            $this->businessSettingRepository->create(data: ['key_name' => $data['type'], 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
        }
    }

    public function storeLandingPageOurSolutionsData(array $data): void
    {
        $value = [
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => 1,
        ];
        if (array_key_exists('id', $data)) {
            $attributes = ['id' => $data['id'], 'key_name' => OUR_SOLUTIONS_DATA, 'settings_type' => LANDING_PAGES_SETTINGS];
            $ourSolutionData = $this->businessSettingRepository->findOneBy(criteria: $attributes);
        }

        if (array_key_exists('image', $data)) {
            $fileName = fileUploader('business/landing-pages/our-solutions/', $data['image']->extension(), $data['image'], (array_key_exists('id', $data) && $ourSolutionData?->value['image'] ? $ourSolutionData?->value['image'] : ''));
            $value['image'] = $fileName;
        }
        if (array_key_exists('id', $data)) {
            if (!array_key_exists('image', $data)) {
                $value['image'] = $ourSolutionData?->value['image'] ?? '';
            }
            $this->businessSettingRepository->update(id: $data['id'], data: ['key_name' => OUR_SOLUTIONS_DATA, 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);

        } else {
            $this->businessSettingRepository->create(data: ['key_name' => OUR_SOLUTIONS_DATA, 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
        }
    }

    public function statusChangeOurSolutions(string|int $id, array $data): ?Model
    {
        $attributes = ['id' => $id, 'key_name' => OUR_SOLUTIONS_DATA, 'settings_type' => LANDING_PAGES_SETTINGS];
        $ourSolutions = $this->businessSettingRepository->findOneBy(criteria: $attributes);
        $value = [
            'title' => $ourSolutions?->value['title'],
            'description' => $ourSolutions?->value['description'],
            'image' => $ourSolutions?->value['image'] ?? '',
            'status' => $data['status'] == "0" ? $data['status'] : "1"
        ];
        return $this->businessSettingRepository->update(id: $id, data: ['key_name' => OUR_SOLUTIONS_DATA, 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
    }

    public function deleteOurSolutions(string|int $id): bool
    {
        $attributes = ['id' => $id, 'key_name' => OUR_SOLUTIONS_DATA, 'settings_type' => LANDING_PAGES_SETTINGS];
        $ourSolutions = $this->businessSettingRepository->findOneBy(criteria: $attributes);
        $image = $ourSolutions?->value['image'] ?? '';
        if ($image) {
            fileRemover('business/landing-pages/our-solutions/', $image);
        }
        return $this->businessSettingRepository->delete(id: $id);
    }

    public function storeLandingPageBusinessStatistics(array $data): void
    {
        $businessStatistic = $this->businessSettingRepository->findOneBy(criteria: [
            'key_name' => BUSINESS_STATISTICS,
            'settings_type' => LANDING_PAGES_SETTINGS
        ]);
        $value = [];
        //start total download
        if (array_key_exists('total_download_image', $data)) {
            $fileName = fileUploader('business/landing-pages/business-statistics/total-download/', $data['total_download_image']->extension(), $data['total_download_image'], $businessStatistic?->value['total_download']['image'] ?? '');
            $value['total_download']['image'] = $fileName;
        } else {
            $value['total_download']['image'] = $businessStatistic?->value['total_download']['image'] ?? '';
        }
        $value['total_download']['count'] = $data['total_download_count'];
        $value['total_download']['content'] = $data['total_download_content'];
        //end total download

        //start complete ride
        if (array_key_exists('complete_ride_image', $data)) {
            $fileName = fileUploader('business/landing-pages/business-statistics/complete-ride/', $data['complete_ride_image']->extension(), $data['complete_ride_image'], $businessStatistic?->value['complete_ride']['image'] ?? '');
            $value['complete_ride']['image'] = $fileName;
        } else {
            $value['complete_ride']['image'] = $businessStatistic?->value['complete_ride']['image'] ?? '';
        }
        $value['complete_ride']['count'] = $data['complete_ride_count'];
        $value['complete_ride']['content'] = $data['complete_ride_content'];
        //end complete ride

        //start happy customer
        if (array_key_exists('happy_customer_image', $data)) {
            $fileName = fileUploader('business/landing-pages/business-statistics/happy-customer/', $data['happy_customer_image']->extension(), $data['happy_customer_image'], $businessStatistic?->value['happy_customer']['image'] ?? '');
            $value['happy_customer']['image'] = $fileName;
        } else {
            $value['happy_customer']['image'] = $businessStatistic?->value['happy_customer']['image'] ?? '';
        }
        $value['happy_customer']['count'] = $data['happy_customer_count'];
        $value['happy_customer']['content'] = $data['happy_customer_content'];
        //end happy customer

        //start support
        if (array_key_exists('support_image', $data)) {
            $fileName = fileUploader('business/landing-pages/business-statistics/support/', $data['support_image']->extension(), $data['support_image'], $businessStatistic?->value['support']['image'] ?? '');
            $value['support']['image'] = $fileName;
        } else {
            $value['support']['image'] = $businessStatistic?->value['support']['image'] ?? '';
        }
        $value['support']['title'] = $data['support_title'];
        $value['support']['content'] = $data['support_content'];
        //end support
        if ($businessStatistic) {
            $this->businessSettingRepository->update(id: $businessStatistic->id, data: ['key_name' => BUSINESS_STATISTICS, 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
        } else {
            $this->businessSettingRepository->create(data: ['key_name' => BUSINESS_STATISTICS, 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
        }
    }

    public function storeLandingPageEarnMoney(array $data)
    {
        $earnMoney = $this->businessSettingRepository->findOneBy(criteria: [
            'key_name' => $data['type'],
            'settings_type' => LANDING_PAGES_SETTINGS
        ]);
        if ($data['type'] === EARN_MONEY) {
            $value = ['title' => $data['title'], 'sub_title' => $data['sub_title']];
        }
        if ($data['type'] === EARN_MONEY_IMAGE) {

            if (array_key_exists('image', $data)) {
                $fileName = fileUploader('business/landing-pages/earn-money/', $data['image']->extension(), $data['image'], $earnMoney->value['image'] ?? '');
                $value['image'] = $fileName;
            } else {
                $value['image'] = $earnMoney->value['image'] ?? '';
            }
        }
        if ($earnMoney) {
            $this->businessSettingRepository->update(id: $earnMoney->id, data: ['key_name' => $data['type'], 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
        } else {
            $this->businessSettingRepository->create(data: ['key_name' => $data['type'], 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
        }
    }

    public function storeLandingPageTestimonial(array $data)
    {
        $value = [];
        $value['reviewer_name'] = $data['reviewer_name'];
        $value['designation'] = $data['designation'];
        $value['rating'] = $data['rating'];
        $value['review'] = $data['review'];
        $value['status'] = "1";

        if (array_key_exists('id', $data)) {
            $attributes = ['id' => $data['id'], 'key_name' => TESTIMONIAL, 'settings_type' => LANDING_PAGES_SETTINGS];
            $testimonial = $this->businessSettingRepository->findOneBy(criteria: $attributes);
        }

        if (array_key_exists('reviewer_image', $data)) {
            $fileName = fileUploader('business/landing-pages/testimonial/', $data['reviewer_image']->extension(), $data['reviewer_image'], (array_key_exists('id', $data) && $testimonial?->value['reviewer_image'] ? $testimonial?->value['reviewer_image'] : ''));
            $value['reviewer_image'] = $fileName;
        }
        if (array_key_exists('id', $data)) {
            if (!array_key_exists('reviewer_image', $data)) {
                $value['reviewer_image'] = $testimonial->value['reviewer_image'] ?? '';
            }
            $this->businessSettingRepository->update(id: $data['id'], data: ['key_name' => TESTIMONIAL, 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);

        } else {
            $this->businessSettingRepository->create(data: ['key_name' => TESTIMONIAL, 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
        }
    }

    public function statusChange(string|int $id, array $data): ?Model
    {
        $attributes = ['id' => $id, 'key_name' => TESTIMONIAL, 'settings_type' => LANDING_PAGES_SETTINGS];
        $testimonial = $this->businessSettingRepository->findOneBy(criteria: $attributes);
        $value = [];
        $value['reviewer_name'] = $testimonial?->value['reviewer_name'];
        $value['designation'] = $testimonial?->value['designation'];
        $value['rating'] = $testimonial?->value['rating'];
        $value['review'] = $testimonial?->value['review'];
        $value['reviewer_image'] = $testimonial?->value['reviewer_image'] ?? '';
        $value['status'] = $data['status'] == "0" ? $data['status'] : "1";
        return $this->businessSettingRepository->update(id: $id, data: ['key_name' => TESTIMONIAL, 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
    }

    public function storeLandingPageCTA(array $data)
    {
        $cta = $this->businessSettingRepository->findOneBy(criteria: [
            'key_name' => $data['type'],
            'settings_type' => LANDING_PAGES_SETTINGS
        ]);
        if ($data['type'] === CTA) {
            $value['title'] = $data['title'];
            $value['sub_title'] = $data['sub_title'];
            $value['play_store']['user_download_link'] = $data['play_store_user_download_link'];
            $value['play_store']['driver_download_link'] = $data['play_store_driver_download_link'];
            $value['app_store']['user_download_link'] = $data['app_store_user_download_link'];
            $value['app_store']['driver_download_link'] = $data['app_store_driver_download_link'];
        } else {
            if (array_key_exists('image', $data)) {
                $fileName = fileUploader('business/landing-pages/cta/', $data['image']->extension(), $data['image'], $cta->value['image'] ?? '');
                $value['image'] = $fileName;
            } else {
                $value['image'] = $file->value['image'] ?? '';
            }
            if (array_key_exists('background_image', $data)) {
                $fileName = fileUploader('business/landing-pages/cta/', $data['background_image']->extension(), $data['background_image'], $cta->value['background_image'] ?? '');
                $value['background_image'] = $fileName;
            } else {
                $value['background_image'] = $cta->value['background_image'] ?? '';
            }
        }
        if ($cta) {
            $this->businessSettingRepository->update(id: $cta->id, data: ['key_name' => $data['type'], 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
        } else {
            $this->businessSettingRepository->create(data: ['key_name' => $data['type'], 'settings_type' => LANDING_PAGES_SETTINGS, 'value' => $value]);
        }
    }

    public function storeEmailConfig(array $data)
    {
        $emailConfig = $this->businessSettingRepository->findOneBy(criteria: [
            'key_name' => EMAIL_CONFIG,
            'settings_type' => EMAIL_CONFIG]);
        if ($emailConfig) {
            $this->businessSettingRepository->update(id: $emailConfig->id, data: ['key_name' => EMAIL_CONFIG, 'settings_type' => EMAIL_CONFIG, 'value' => $data]);
        } else {
            $this->businessSettingRepository->create(data: ['key_name' => EMAIL_CONFIG, 'settings_type' => EMAIL_CONFIG, 'value' => $data]);
        }
    }
    public function storeGoogleMapApi(array $data)
    {
        $googleMapApi = $this->businessSettingRepository->findOneBy(criteria: [
            'key_name' => GOOGLE_MAP_API,
            'settings_type' => GOOGLE_MAP_API
        ]);
        if ($googleMapApi) {
            $this->businessSettingRepository->update(id: $googleMapApi->id, data: ['key_name' => GOOGLE_MAP_API, 'settings_type' => GOOGLE_MAP_API, 'value' => $data]);
        } else {
            $this->businessSettingRepository->create(data: ['key_name' => GOOGLE_MAP_API, 'settings_type' => GOOGLE_MAP_API, 'value' => $data]);
        }
    }
    public function storeRecaptha(array $data)
    {
        $recaptcha = $this->businessSettingRepository->findOneBy(criteria: [
            'settings_type' => RECAPTCHA,
            'key_name' => RECAPTCHA
        ]);
        if ($recaptcha) {
            $this->businessSettingRepository->update(id: $recaptcha->id, data: ['key_name' => RECAPTCHA, 'settings_type' => RECAPTCHA, 'value' => $data]);
        } else {
            $this->businessSettingRepository->create(data: ['key_name' => RECAPTCHA, 'settings_type' => RECAPTCHA, 'value' => $data]);
        }
    }
}

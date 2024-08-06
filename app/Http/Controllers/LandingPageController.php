<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;
use Modules\BusinessManagement\Service\Interface\BusinessSettingServiceInterface;

class LandingPageController extends Controller
{
    use AuthorizesRequests;

    protected $businessSetting;

    public function __construct(BusinessSettingServiceInterface $businessSetting)
    {
        $this->businessSetting = $businessSetting;
    }

    public function index(): Factory|View|Application
    {
        $cacheKey = 'landing_page_settings';
        $cacheDuration = now()->addMonth(1);

        $settings = Cache::remember($cacheKey, $cacheDuration, function () {
            return [
                'introSection' => $this->businessSetting->findOneBy(['key_name' => INTRO_SECTION, 'settings_type' => LANDING_PAGES_SETTINGS]),
                'introSectionImage' => $this->businessSetting->findOneBy(['key_name' => INTRO_SECTION_IMAGE, 'settings_type' => LANDING_PAGES_SETTINGS]),
                'ourSolutionSection' => $this->businessSetting->findOneBy(['key_name' => OUR_SOLUTIONS_SECTION, 'settings_type' => LANDING_PAGES_SETTINGS]),
                'ourSolutionSectionList' => $this->businessSetting->getBy(['key_name' => OUR_SOLUTIONS_DATA, 'settings_type' => LANDING_PAGES_SETTINGS]),
                'businessStatistics' => $this->businessSetting->findOneBy(['key_name' => BUSINESS_STATISTICS, 'settings_type' => LANDING_PAGES_SETTINGS]),
                'earnMoney' => $this->businessSetting->findOneBy(['key_name' => EARN_MONEY, 'settings_type' => LANDING_PAGES_SETTINGS]),
                'earnMoneyImage' => $this->businessSetting->findOneBy(['key_name' => EARN_MONEY_IMAGE, 'settings_type' => LANDING_PAGES_SETTINGS]),
                'testimonials' => $this->businessSetting->getBy(['key_name' => TESTIMONIAL, 'settings_type' => LANDING_PAGES_SETTINGS],[ paginationLimit(), 1]),
                'cta' => $this->businessSetting->findOneBy(['key_name' => CTA, 'settings_type' => LANDING_PAGES_SETTINGS]),
                'ctaImage' => $this->businessSetting->findOneBy(['key_name' => CTA_IMAGE, 'settings_type' => LANDING_PAGES_SETTINGS]),
                'business_name' => $this->businessSetting->findOneBy(['key_name' => 'business_name', 'settings_type' => BUSINESS_INFORMATION]),
            ];
        });

        $ourSolutionSectionListCount = collect($settings['ourSolutionSectionList'])->filter(function ($item) {
            return $item->value['status'] == 1;
        })->count();

        $testimonialListCount = collect($settings['testimonials'])->filter(function ($item) {
            return $item->value['status'] == 1;
        })->count();

        return view('landing-page.index', [
            'introSection' => $settings['introSection'],
            'introSectionImage' => $settings['introSectionImage'],
            'ourSolutionSection' => $settings['ourSolutionSection'],
            'ourSolutionSectionListCount' => $ourSolutionSectionListCount,
            'ourSolutionSectionList' => $settings['ourSolutionSectionList'],
            'business_name' => $settings['business_name'],
            'businessStatistics' => $settings['businessStatistics'],
            'earnMoney' => $settings['earnMoney'],
            'earnMoneyImage' => $settings['earnMoneyImage'],
            'testimonials' => $settings['testimonials'],
            'testimonialListCount' => $testimonialListCount,
            'cta' => $settings['cta'],
            'ctaImage' => $settings['ctaImage']
        ]);
    }

    public function aboutUs()
    {
        $data = Cache::remember('about_us', now()->addMonth(1), function () {
            return $this->businessSetting->findOneBy(['key_name' => 'about_us', 'settings_type' => PAGES_SETTINGS]);
        });

        return view('landing-page.about', compact('data'));
    }

    public function contactUs()
    {
        return view('landing-page.contact');
    }

    public function privacy()
    {
        $data = Cache::remember('privacy_policy', now()->addMonth(1), function () {
            return $this->businessSetting->findOneBy(['key_name' => 'privacy_policy', 'settings_type' => PAGES_SETTINGS]);
        });

        return view('landing-page.privacy', compact('data'));
    }

    public function terms()
    {
        $data = Cache::remember('terms_and_conditions', now()->addMonth(1), function () {
            return $this->businessSetting->findOneBy(['key_name' => 'terms_and_conditions', 'settings_type' => PAGES_SETTINGS]);
        });

        return view('landing-page.terms', compact('data'));
    }
}

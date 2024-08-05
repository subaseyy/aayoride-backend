<?php

namespace Modules\BusinessManagement\Http\Controllers\Web\New\Admin\SystemSetting;

use App\Http\Controllers\BaseController;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Modules\BusinessManagement\Service\Interface\LanguageSettingServiceInterface;

class LanguageController extends BaseController
{
    use AuthorizesRequests;

    protected $languageSettingService;

    public function __construct(LanguageSettingServiceInterface $languageSettingService)
    {
        parent::__construct($languageSettingService);
        $this->languageSettingService = $languageSettingService;
    }

    public function index(?Request $request, string $type = null): \Illuminate\View\View|\Illuminate\Database\Eloquent\Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('business_view');
        $language = $this->languageSettingService->findOneBy(criteria: ['key_name' => SYSTEM_LANGUAGE, 'settings_type' => LANGUAGE_SETTINGS]);
        if (!$language) {
            $this->languageSettingService->create([
                'key_name' => SYSTEM_LANGUAGE,
                'value' => [[
                    "id" => 1,
                    "direction" => "ltr",
                    "code" => "en",
                    "status" => 1,
                    "default" => true
                ]],
                'settings_type' => LANGUAGE_SETTINGS
            ]);
        }
        return view('businessmanagement::admin.system-settings.language.index', compact('language'));
    }

    public function store(Request $request)
    {
        $this->authorize('business_edit');
        $language = $this->languageSettingService->findOneBy(criteria: ['key_name' => SYSTEM_LANGUAGE, 'settings_type' => LANGUAGE_SETTINGS]);
        if ($language) {
            foreach ($language['value'] as $key => $data) {
                if ($data['code'] == $request['code']) {
                    Toastr::warning(DEFAULT_EXISTS_203['message']);
                    return back();
                }

            }
            $this->languageSettingService->storeLanguage($request->all());
            Toastr::success(DEFAULT_STORE_200['message']);
        }
        return back();
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('business_edit');
        $this->languageSettingService->updateLanguage($request->all());
        Toastr::success(DEFAULT_UPDATE_200['message']);
        return back();
    }

    public function delete($lang): RedirectResponse
    {
        $this->authorize('business_edit');
        $language = $this->languageSettingService->findOneBy(criteria: ['key_name' => SYSTEM_LANGUAGE, 'settings_type' => LANGUAGE_SETTINGS]);
        if ($language) {
            foreach ($language['value'] as $data) {
                if ($data['code'] == $lang && array_key_exists('default', $data) && $data['default']) {
                    Toastr::error(LANGUAGE_UPDATE_FAIL_200['message']);
                    return back();
                }
            }
            $this->languageSettingService->deleteLanguage($lang);
            Toastr::success(DEFAULT_DELETE_200['message']);
        }
        return back();
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $this->authorize('business_edit');
        $language = $this->languageSettingService->findOneBy(criteria: ['key_name' => SYSTEM_LANGUAGE, 'settings_type' => LANGUAGE_SETTINGS]);
        if ($language) {
            foreach ($language['value'] as $key => $data) {
                if ($data['code'] == $request['code']) {
                    if ($data['status'] == 1 && $data['default']) {
                        return response()->json([
                            'message' => LANGUAGE_UPDATE_FAIL_200['message'],
                            'status' => 0
                        ]);
                    }
                }
            }
        }
        $this->languageSettingService->changeLanguageStatus($request->all());
        return response()->json([
            'message' => DEFAULT_UPDATE_200['message'],
            'status' => 1
        ]);
    }

    public function updateDefaultStatus(Request $request): RedirectResponse
    {
        $this->authorize('business_edit');
        $this->languageSettingService->changeLanguageDefaultStatus($request->all());
        Toastr::success(DEFAULT_UPDATE_200['message']);
        return back();
    }

    public function translate($lang): Factory|View|Application
    {
        $translateData = $this->languageSettingService->translate($lang);
        return view('businessmanagement::admin.system-settings.language.translate', compact('lang', 'translateData'));
    }

    public function translateSubmit(Request $request, $lang)
    {
        $this->authorize('business_edit');
        $this->languageSettingService->storeTranslate($request->all(), $lang);
    }

    public function autoTranslate(Request $request, $lang): JsonResponse
    {
        $this->authorize('business_edit');
        $translated = $this->languageSettingService->autoTranslate($request->all(), $lang);
        return response()->json([
            'translated_data' => $translated
        ]);
    }

}

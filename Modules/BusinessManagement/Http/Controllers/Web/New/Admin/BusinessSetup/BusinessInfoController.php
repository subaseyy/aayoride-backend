<?php

namespace Modules\BusinessManagement\Http\Controllers\Web\New\Admin\BusinessSetup;

use App\Http\Controllers\BaseController;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Modules\BusinessManagement\Http\Requests\BusinessInfoStoreOrUpdateRequest;
use Modules\BusinessManagement\Http\Requests\BusinessSettingStoreOrUpdateRequest;
use Modules\BusinessManagement\Service\Interface\BusinessSettingServiceInterface;

class BusinessInfoController extends BaseController
{
    use AuthorizesRequests;

    protected $businessSettingService;

    public function __construct(BusinessSettingServiceInterface $businessSettingService)
    {
        parent::__construct($businessSettingService);
        $this->businessSettingService = $businessSettingService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('business_view');
        $settings = $this->businessSettingService
            ->getBy(criteria: ['settings_type' => BUSINESS_INFORMATION]);

        return view('businessmanagement::admin.business-setup.index', compact('settings'));
    }

    public function store(BusinessInfoStoreOrUpdateRequest $request)
    {
        $this->authorize('business_edit');
        $this->businessSettingService->storeBusinessInfo($request->validated());
        Toastr::success(BUSINESS_SETTING_UPDATE_200['message']);
        return back();
    }

    public function updateBusinessSetting(Request $request): JsonResponse
    {
        $this->authorize('business_edit');
        $businessInfo = $this->businessSettingService->findOneBy(criteria: ['key_name' => $request['name'], 'settings_type' => $request['type']]);
        if ($businessInfo) {
            $data = $this->businessSettingService
                ->update(id: $businessInfo->id, data: ['key_name' => $request['name'], 'settings_type' => $request['type'], 'value' => $request['value']]);
        } else {
            $data = $this->businessSettingService
                ->create(data: ['key_name' => $request['name'], 'settings_type' => $request['type'], 'value' => $request['value']]);
        }
        return response()->json($data);
    }

    public function settings()
    {
        $settings = $this->businessSettingService
            ->getBy(criteria: ['settings_type' => 'business_settings']);
        return view('businessmanagement::admin.business-setup.settings', compact('settings'));
    }

    public function updateSettings(BusinessSettingStoreOrUpdateRequest $request): RedirectResponse
    {
        $this->authorize('business_edit');
        $this->businessSettingService->updateSetting($request->validated());
        Toastr::success(BUSINESS_SETTING_UPDATE_200['message']);
        return back();
    }

    public function maintenance(Request $request)
    {
        $this->authorize('super-admin');
        $data = $this->businessSettingService->maintenance(data: $request->all());
        return response()->json($data);
    }
}

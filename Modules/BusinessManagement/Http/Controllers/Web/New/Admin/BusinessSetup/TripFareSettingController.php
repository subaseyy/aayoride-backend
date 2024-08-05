<?php

namespace Modules\BusinessManagement\Http\Controllers\Web\New\Admin\BusinessSetup;

use App\Http\Controllers\BaseController;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Modules\BusinessManagement\Http\Requests\CancellationReasonStoreOrUpdateRequest;
use Modules\BusinessManagement\Http\Requests\TripFareSettingStoreOrUpdateRequest;
use Modules\BusinessManagement\Service\Interface\BusinessSettingServiceInterface;
use Modules\BusinessManagement\Service\Interface\CancellationReasonServiceInterface;

class TripFareSettingController extends BaseController
{
    protected $businessSettingService;
    protected $cancellationReasonService;

    public function __construct(BusinessSettingServiceInterface $businessSettingService, CancellationReasonServiceInterface $cancellationReasonService)
    {
        parent::__construct($businessSettingService);
        $this->businessSettingService = $businessSettingService;
        $this->cancellationReasonService = $cancellationReasonService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('business_view');
        $settings = $this->businessSettingService->getBy(criteria: ['settings_type' => TRIP_SETTINGS]);
        return view('businessmanagement::admin.business-setup.fare_and_penalty', compact('settings'));
    }

    public function tripIndex(Request $request)
    {
        $this->authorize('business_view');
        $settings = $this->businessSettingService->getBy(criteria: ['settings_type' => TRIP_SETTINGS]);
        $cancellationReasons = $this->cancellationReasonService->getBy(orderBy: ['created_at'=>'desc'], limit: paginationLimit(),offset: $request?->page??1);
        return view('businessmanagement::admin.business-setup.trips', compact('settings','cancellationReasons'));
    }

    public function store(TripFareSettingStoreOrUpdateRequest $request)
    {
        $this->authorize('business_edit');
        $this->businessSettingService->storeTripFareSetting($request->validated());
        Toastr::success(BUSINESS_SETTING_UPDATE_200['message']);
        return back();
    }

    #cancellation reason
    public function storeCancellationReason(CancellationReasonStoreOrUpdateRequest $request)
    {
        $this->authorize('business_edit');
        $this->cancellationReasonService->create(data: $request->validated());
        Toastr::success(translate('Cancellation message stored successfully'));
        return redirect()->back();
    }
    public function editCancellationReason($id)
    {
        $this->authorize('business_edit');
        $cancellationReason = $this->cancellationReasonService->findOne(id: $id);
        if (!$cancellationReason){
            Toastr::error(translate('Cancellation reason not found'));
            return redirect()->back();
        }
        return view('businessmanagement::admin.business-setup.edit-cancellation-reason', compact('cancellationReason'));
    }
    public function updateCancellationReason($id, CancellationReasonStoreOrUpdateRequest $request)
    {
        #TODO
        $this->authorize('business_edit');
        $this->cancellationReasonService->update(id: $id,data: $request->validated());
        Toastr::success(translate('Cancellation message updated successfully'));
        return redirect()->back();
    }

    public function destroyCancellationReason(string $id)
    {
        $this->authorize('vehicle_delete');
        $this->cancellationReasonService->delete(id: $id);
        Toastr::success(translate('Cancellation message deleted successfully.'));
        return redirect()->route('admin.business.setup.trip-fare.trips');
    }

    public function statusCancellationReason(Request $request): JsonResponse
    {
        $this->authorize('vehicle_edit');
        $request->validate([
            'status' => 'boolean'
        ]);
        $model = $this->cancellationReasonService->statusChange(id: $request->id, data: $request->all());
        return response()->json($model);
    }
}

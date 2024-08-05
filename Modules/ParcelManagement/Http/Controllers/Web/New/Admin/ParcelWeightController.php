<?php

namespace Modules\ParcelManagement\Http\Controllers\Web\New\Admin;

use App\Http\Controllers\BaseController;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Modules\BusinessManagement\Interfaces\BusinessSettingInterface;
use Modules\ParcelManagement\Http\Requests\ParcelWeightStoreOrUpdateRequest;
use Modules\ParcelManagement\Service\Interface\ParcelWeightServiceInterface;
use Modules\ParcelManagement\Service\ParcelWeightService;

class ParcelWeightController extends BaseController
{
    use AuthorizesRequests;

    protected $parcelWeightService;
    protected $businessInfo;
    public function __construct(ParcelWeightServiceInterface $parcelWeightService, BusinessSettingInterface $businessInfo)
    {
        parent::__construct($parcelWeightService);
        $this->parcelWeightService = $parcelWeightService;
        $this->businessInfo = $businessInfo;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('parcel_view');
        $weights = $this->parcelWeightService->index(criteria: $request?->all(), orderBy:['min_weight'=>'asc'], limit: paginationLimit(), offset: $request['page'] ?? 1);
        $weightUnit = $this->businessInfo->getBy(column: "dummy", value: 1, attributes: ['key_name' => 'parcel_weight_unit', 'settings_type' => 'business_information']);
        return view('parcelmanagement::admin.attributes.weight.index', compact('weights', 'weightUnit'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->authorize('parcel_add');

        return view('parcelmanagement::create');
    }

    public function store(ParcelWeightStoreOrUpdateRequest $request)
    {
        $this->authorize('parcel_add');
        $weightLists = $this->parcelWeightService->getAll();
        foreach ($weightLists as $list) {
            if ($request->min_weight <= $list->max_weight && $request->max_weight >= $list->min_weight) {
                Toastr::error(ucfirst(PARCEL_WEIGHT_EXISTS_403['message']));
                return back();
            }
        }

        $this->parcelWeightService->create(data: $request->validated());
        Toastr::success(ucfirst(PARCEL_WEIGHT_STORE_200['message']));
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->authorize('parcel_edit');

        $weight = $this->parcelWeightService->findOne(id: $id);
        $weightUnit = $this->businessInfo->getBy(column: "dummy", value: 1, attributes: ['key_name' => 'parcel_weight_unit', 'settings_type' => 'business_information']);
        return view('parcelmanagement::admin.attributes.weight.edit', compact('weight', 'weightUnit'));
    }

    public function update(ParcelWeightStoreOrUpdateRequest $request, $id)
    {
        $this->authorize('parcel_edit');


        $weightLists = $this->parcelWeightService->getAll();
        // Exclude the current weight being updated
        $updatedWeight = $this->parcelWeightService->findOne($id);

        foreach ($weightLists as $list) {
            // Skip the current weight in the check
            if ($list->id == $updatedWeight->id) {
                continue;
            }

            if ($request->min_weight <= $list->max_weight && $request->max_weight >= $list->min_weight) {
                Toastr::error(ucfirst(PARCEL_WEIGHT_EXISTS_403['message']));
                return back();
            }
        }



        $this->parcelWeightService->update(id: $id, data: $request->validated());

        Toastr::success(PARCEL_WEIGHT_UPDATE_200['message']);
        return redirect()->route('admin.parcel.attribute.weight.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $this->authorize('parcel_delete');

        $this->parcelWeightService->delete(id: $id);

        Toastr::success(PARCEL_WEIGHT_DESTROY_200['message']);
        return redirect()->route('admin.parcel.attribute.weight.index');
    }

    /**
     * Update the status specified resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function status(Request $request): JsonResponse
    {
        $this->authorize('parcel_edit');

        $model = $this->parcelWeightService->statusChange(id: $request->id, data: $request->all());

        return response()->json($model);
    }


    public function download(Request $request): mixed
    {
        $this->authorize('parcel_export');
        $data = $this->parcelWeightService->export(criteria: $request->all(), orderBy: ['created_at' => 'desc']);
        return exportData($data, $request['file'], 'parcelmanagement::admin.attributes.weight.print');
    }

    public function log(Request $request)
    {
        $this->authorize('parcel_log');

        $request->merge([
            'logable_type' => 'Modules\ParcelManagement\Entities\ParcelWeight',
        ]);

        return log_viewer($request->all());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function trashed(Request $request): View
    {

        $this->authorize('super-admin');

        $search = $request->has('search') ? $request->search : null;
        $weights = $this->parcelWeightService->trashedData(criteria: $request->all(), limit: paginationLimit());
        $weightUnit = $this->businessInfo->getBy(column: "dummy", value: 1, attributes: ['key_name' => 'parcel_weight_unit', 'settings_type' => 'business_information']);

        return view('parcelmanagement::admin.attributes.weight.trashed', compact('weights', 'search', 'weightUnit'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');

        $this->parcelWeightService->restoreData(id: $id);

        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.parcel.attribute.weight.index');
    }

    public function permanentDelete($id)
    {
        $this->authorize('super-admin');
        $this->parcelWeightService->permanentDelete(id: $id);
        Toastr::success(PARCEL_WEIGHT_DESTROY_200['message']);
        return back();
    }
}

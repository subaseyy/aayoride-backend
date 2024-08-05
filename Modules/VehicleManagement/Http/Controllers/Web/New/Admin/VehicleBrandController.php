<?php

namespace Modules\VehicleManagement\Http\Controllers\Web\New\Admin;

use App\Http\Controllers\BaseController;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Modules\VehicleManagement\Http\Requests\VehicleBrandStoreUpdateRequest;
use Modules\VehicleManagement\Service\Interface\VehicleBrandServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VehicleBrandController extends BaseController
{
    use AuthorizesRequests;

    protected $vehicleBrandService;

    public function __construct(VehicleBrandServiceInterface $vehicleBrandService)
    {
        parent::__construct($vehicleBrandService);
        $this->vehicleBrandService = $vehicleBrandService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('vehicle_view');
        $brands = $this->vehicleBrandService->index(criteria: $request?->all(), relations: ['vehicles'], orderBy: ['created_at' => 'desc'], limit: paginationLimit(), offset: $request['page']??1);
        return view('vehiclemanagement::admin.brand.index', compact('brands'));
    }

    public function store(VehicleBrandStoreUpdateRequest $request): RedirectResponse
    {
        $this->authorize('vehicle_add');
        $this->vehicleBrandService->create(data: $request->validated());
        Toastr::success(ucfirst(BRAND_CREATE_200['message']));
        return back();

    }

    public function edit(string $id): Renderable
    {
        $this->authorize('vehicle_edit');
        $brand = $this->vehicleBrandService->findOne(id: $id);
        return view('vehiclemanagement::admin.brand.edit', compact('brand'));
    }

    public function update(VehicleBrandStoreUpdateRequest $request, string $id): RedirectResponse
    {
        $this->authorize('vehicle_edit');
        $this->vehicleBrandService->update(id: $id, data: $request->validated());
        Toastr::success(BRAND_UPDATE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.brand.index');

    }

    public function destroy(string $id): RedirectResponse
    {
        $this->authorize('vehicle_delete');
        $this->vehicleBrandService->delete(id: $id);
        Toastr::success(BRAND_DELETE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.brand.index');
    }

    public function status(Request $request): JsonResponse
    {
        $this->authorize('vehicle_edit');
        $request->validate([
            'status' => 'boolean'
        ]);
        $model = $this->vehicleBrandService->statusChange(id: $request->id, data: $request->all());
        return response()->json($model);
    }

    public function trashed(Request $request): View
    {
        $this->authorize('super-admin');
        $brands = $this->vehicleBrandService->getBy(criteria: $request->all(), limit: paginationLimit(), onlyTrashed: true);
        return view('vehiclemanagement::admin.brand.trashed', compact('brands'));
    }

    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');
        $this->vehicleBrandService->restoreData(id: $id);
        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.brand.index');
    }

    public function permanentDelete($id)
    {
        $this->authorize('super-admin');
        $this->vehicleBrandService->permanentDelete(id: $id);
        Toastr::success(BRAND_DELETE_200['message']);
        return back();
    }

    public function getAllAjax(Request $request): JsonResponse
    {
        $brands = $this->vehicleBrandService->index(criteria: $request->all());
        $selectBrands = $brands->map(function ($items, $key) {
            return [
                'text' => $items->name,
                'id' => $items->id
            ];
        });
        return response()->json($selectBrands);
    }

    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('vehicle_export');
        $data = $this->vehicleBrandService->export(criteria: $request->all(), relations: ['vehicles'], orderBy: ['created_at' => 'desc']);
        return exportData($data, $request['file'], 'vehiclemanagement::admin.brand.print');
    }

    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('vehicle_log');
        $request->merge([
            'logable_type' => 'Modules\VehicleManagement\Entities\VehicleBrand',
        ]);
        return log_viewer($request->all());

    }
}

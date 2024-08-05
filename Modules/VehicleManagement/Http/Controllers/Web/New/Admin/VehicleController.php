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
use Modules\VehicleManagement\Http\Requests\VehicleStoreUpdateRequest;
use Modules\VehicleManagement\Service\Interface\VehicleCategoryServiceInterface;
use Modules\VehicleManagement\Service\Interface\VehicleServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VehicleController extends BaseController
{
    use AuthorizesRequests;

    protected $vehicleService;
    protected $vehicleCategoryService;

    public function __construct(VehicleServiceInterface $vehicleService, VehicleCategoryServiceInterface $vehicleCategoryService)
    {
        parent::__construct($vehicleService);
        $this->vehicleService = $vehicleService;
        $this->vehicleCategoryService = $vehicleCategoryService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('vehicle_view');
        $vehicles = $this->vehicleService->index(criteria: $request?->all(), relations: ['model', 'brand'],orderBy: ['updated_at'=>'desc'], limit: paginationLimit(), offset: $request['page']??1);
        $categories = $this->vehicleCategoryService->getAll(relations: ['vehicles']);
        return view('vehiclemanagement::admin.vehicle.index', compact('vehicles', 'categories'));
    }

    public function create(): Renderable
    {
        $this->authorize('vehicle_add');

        return view('vehiclemanagement::admin.vehicle.create');
    }

    public function store(VehicleStoreUpdateRequest $request): RedirectResponse
    {
        $this->authorize('vehicle_add');
        $this->vehicleService->create(data: $request->validated());
        Toastr::success(ucfirst(VEHICLE_CREATE_200['message']));
        return redirect()->route('admin.vehicle.index');
    }

    public function show(string $id): Renderable
    {
        $this->authorize('vehicle_view');
        $relations = ['brand', 'model', 'category', 'driver'];
        $vehicle = $this->vehicleService->findOne(id: $id, relations: $relations);
        return view('vehiclemanagement::admin.vehicle.show', compact('vehicle'));
    }

    public function edit(string $id): Renderable
    {
        $this->authorize('vehicle_edit');
        $relations = ['brand', 'model', 'category', 'driver'];
        $vehicle = $this->vehicleService->findOne(id: $id, relations: $relations);
        return view('vehiclemanagement::admin.vehicle.edit', compact('vehicle'));
    }

    public function update(VehicleStoreUpdateRequest $request, string $id): RedirectResponse
    {
        $this->authorize('vehicle_edit');
        $this->vehicleService->update(id: $id, data: $request->validated());
        Toastr::success(VEHICLE_UPDATE_200['message']);
        return redirect()->route('admin.vehicle.index');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->authorize('vehicle_delete');
        $this->vehicleService->delete(id: $id);
        Toastr::success(DEFAULT_DELETE_200['message']);
        return redirect()->route('admin.vehicle.index');
    }

    public function status(Request $request): JsonResponse
    {
        $this->authorize('vehicle_edit');
        $model = $this->vehicleService->statusChange(id: $request->id, data: $request->all());
        $push = getNotification('vehicle_approved');
        if ($model && $request->status && $model?->driver->fcm_token) {
            sendDeviceNotification(
                fcm_token: $model?->driver->fcm_token,
                title: translate($push['title']),
                description: translate($push['description']),
                action: 'vehicle_approved',
                user_id: $model?->driver_id
            );
        }
        return response()->json($model);
    }

    public function trashed(Request $request): View
    {
        $this->authorize('super-admin');
        $vehicles = $this->vehicleService->getBy(criteria: $request->all(), limit: paginationLimit(), offset: $request['page']??1, onlyTrashed: true);
        return view('vehiclemanagement::admin.vehicle.trashed', compact('vehicles'));
    }

    public function restore(string $id): RedirectResponse
    {
        $this->authorize('super-admin');
        $this->vehicleService->restoreData(id: $id);
        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.vehicle.index');

    }

    public function permanentDelete($id)
    {
        $this->authorize('super-admin');
        $this->vehicleService->permanentDelete(id: $id);
        Toastr::success(DEFAULT_DELETE_200['message']);
        return back();
    }

    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('vehicle_export');
        $data = $this->vehicleService->export(criteria: $request->all(), relations: ['category', 'model', 'brand'], orderBy: ['created_at' => 'desc']);
        return exportData($data, $request['file'], 'vehiclemanagement::admin.vehicle.print');
    }

    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $request->merge([
            'logable_type' => 'Modules\VehicleManagement\Entities\Vehicle',
        ]);
        return log_viewer($request->all());
    }
}

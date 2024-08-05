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
use Modules\VehicleManagement\Http\Requests\VehicleModelStoreUpdateRequest;
use Modules\VehicleManagement\Service\Interface\VehicleModelServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VehicleModelController extends BaseController
{
    use AuthorizesRequests;

    protected $vehicleModelService;

    public function __construct(VehicleModelServiceInterface $vehicleModelService)
    {
        parent::__construct($vehicleModelService);
        $this->vehicleModelService = $vehicleModelService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('vehicle_view');
        $relations = ['vehicles'];
        $models = $this->vehicleModelService->index(criteria: $request?->all(), relations: $relations, limit: paginationLimit(), offset: $request['page']??1);
        return view('vehiclemanagement::admin.model.index', compact('models'));
    }

    public function store(VehicleModelStoreUpdateRequest $request): RedirectResponse
    {
        $this->authorize('vehicle_add');
        $this->vehicleModelService->create(data: $request->validated());

        Toastr::success(MODEL_CREATE_200['message']);
        return back();
    }

    public function edit(string $id): Renderable
    {
        $this->authorize('vehicle_edit');
        $relations = ['brand', 'vehicles'];
        $model = $this->vehicleModelService->findOne(id: $id, relations: $relations);
        return view('vehiclemanagement::admin.model.edit', compact('model'));
    }

    public function update(VehicleModelStoreUpdateRequest $request, string $id): RedirectResponse
    {
        $this->authorize('vehicle_edit');
        $this->vehicleModelService->update(id: $id, data: $request->validated());
        Toastr::success(MODEL_UPDATE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.model.index');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->authorize('vehicle_delete');
        $this->vehicleModelService->delete(id: $id);
        Toastr::success(DEFAULT_DELETE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.model.index');
    }

    public function status(Request $request): JsonResponse
    {
        $this->authorize('vehicle_edit');
        $model = $this->vehicleModelService->statusChange(id: $request->id, data: $request->all());
        return response()->json($model);
    }

    public function trashed(Request $request): View
    {
        $this->authorize('super-admin');
        $models = $this->vehicleModelService->getBy(criteria: $request->all(), limit: paginationLimit(), offset: $request['page']??1, onlyTrashed: true);
        return view('vehiclemanagement::admin.model.trashed', compact('models'));
    }

    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');
        $this->vehicleModelService->restoreData($id);
        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.model.index');

    }

    public function permanentDelete($id)
    {
        $this->authorize('super-admin');
        $this->vehicleModelService->permanentDelete(id: $id);
        Toastr::success(DEFAULT_DELETE_200['message']);
        return back();
    }

    public function ajax_models(Request $request, $brand_id): JsonResponse
    {
        $attributes = ['brand_id' => $brand_id, 'is_active' => 1];
        $models = $this->vehicleModelService->getBy(criteria: $attributes);
        return response()->json([
            'template' => view('vehiclemanagement::admin.partials._model-selector', compact('models'))->render(),
        ]);
    }


    public function ajax_models_child(Request $request, $brand_id): JsonResponse
    {
        $attributes = ['brand_id' => $brand_id, 'is_active' => 1];
        $models = $this->vehicleModelService->getBy(criteria: $attributes);
        $model_id = $request->model_id ?? null;

        return response()->json([
            'template' => view('vehiclemanagement::admin.partials._model-selector', compact('models', 'model_id'))->render()
        ]);
    }


    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('vehicle_export');
        $data = $this->vehicleModelService->export(criteria: $request->all(), relations: ['vehicles'], orderBy: ['created_at' => 'desc']);
        return exportData($data, $request['file'], 'vehiclemanagement::admin.model.print');
    }


    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('vehicle_log');

        $request->merge([
            'logable_type' => 'Modules\VehicleManagement\Entities\VehicleModel',
        ]);
        return log_viewer($request->all());
    }
}

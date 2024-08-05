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
use Modules\VehicleManagement\Http\Requests\VehicleCategoryStoreUpdateRequest;
use Modules\VehicleManagement\Service\Interface\VehicleCategoryServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VehicleCategoryController extends BaseController
{
    use AuthorizesRequests;

    protected $vehicleCategoryService;
    public function __construct(VehicleCategoryServiceInterface $vehicleCategoryService)
    {
        parent::__construct($vehicleCategoryService);
        $this->vehicleCategoryService = $vehicleCategoryService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('vehicle_view');
        $relations = ['vehicles'];
        $categories = $this->vehicleCategoryService->index(criteria: $request?->all(), relations: $relations, orderBy: ['created_at' => 'desc'], limit: paginationLimit(), offset: $request['page']??1);
        return view('vehiclemanagement::admin.category.index', compact('categories'));
    }

    public function store(VehicleCategoryStoreUpdateRequest $request): RedirectResponse
    {
        $this->authorize('vehicle_add');
        $this->vehicleCategoryService->create(data: $request->validated());
        Toastr::success(CATEGORY_CREATE_200['message']);
        return back();
    }

    public function edit(string $id): Renderable
    {
        $this->authorize('vehicle_edit');
        $category = $this->vehicleCategoryService->findOne(id: $id);
        return view('vehiclemanagement::admin.category.edit', compact('category'));
    }

    public function update(VehicleCategoryStoreUpdateRequest $request, string $id): RedirectResponse
    {
        $this->authorize('vehicle_edit');
        $this->vehicleCategoryService->update(id: $id, data: $request->validated());

        Toastr::success(CATEGORY_UPDATE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.category.index');

    }

    public function destroy(string $id): RedirectResponse
    {
        $this->authorize('vehicle_delete');
        $this->vehicleCategoryService->delete(id: $id);
        Toastr::success(DEFAULT_DELETE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.category.index');
    }

    public function status(Request $request): JsonResponse
    {
        $this->authorize('vehicle_edit');
        $model = $this->vehicleCategoryService->statusChange(id: $request->id, data: $request->all());
        return response()->json($model);
    }

    public function trashed(Request $request): View
    {
        $this->authorize('super-admin');
        $categories = $this->vehicleCategoryService->getBy(criteria: $request->all(),limit: paginationLimit(),onlyTrashed: true);
        return view('vehiclemanagement::admin.category.trashed', compact('categories'));
    }


    public function restore(string $id): RedirectResponse
    {
        $this->authorize('super-admin');
        $this->vehicleCategoryService->restoreData($id);
        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.category.index');

    }

    public function permanentDelete($id)
    {
        $this->authorize('super-admin');
        $this->vehicleCategoryService->permanentDelete(id: $id);
        Toastr::success(DEFAULT_DELETE_200['message']);
        return back();
    }

    public function getAllAjax(Request $request): JsonResponse
    {
        $category = $this->vehicleCategoryService->index(criteria: $request->all());
        $selectCategories = $category->map(function ($items, $key) {
            return [
                'text' => $items->name,
                'id' => $items->id
            ];
        });
        return response()->json($selectCategories);
    }


    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('vehicle_export');
        $data = $this->vehicleCategoryService->export( criteria: $request->all(), relations: ['vehicles'], orderBy: ['created_at' => 'desc']);
        return exportData($data, $request['file'], 'vehiclemanagement::admin.category.print');
    }

    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('vehicle_log');

        $request->merge([
            'logable_type' => 'Modules\VehicleManagement\Entities\VehicleCategory',
        ]);
        return log_viewer($request->all());
    }
}

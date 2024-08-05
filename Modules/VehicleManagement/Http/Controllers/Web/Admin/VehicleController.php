<?php

namespace Modules\VehicleManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\Entities\VehicleCategory;
use Modules\VehicleManagement\Interfaces\VehicleCategoryInterface;
use Modules\VehicleManagement\Interfaces\VehicleInterface;
use Modules\VehicleManagement\Repositories\VehicleCategoryRepository;
use Modules\VehicleManagement\Repositories\VehicleRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;


class VehicleController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private VehicleInterface $vehicle,
        private VehicleCategoryInterface $category)
    {

    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Renderable
     */
    public function index(Request $request): Renderable
    {
        $this->authorize('vehicle_view');

        $validated = $request->validate([
            'value' => 'in:all,active,inactive',
            'query' => 'sometimes',
            'search' => 'sometimes'
        ]);
        $vehicles = $this->vehicle->get(limit: paginationLimit(), offset: 1, attributes: $validated, relations: ['model', 'brand']);

        $categories = $this->category->get(limit: 999999, offset: 1, attributes: ['get' => true], relations: ['vehicles']);

        return view('vehiclemanagement::admin.vehicle.index',
            [
                'vehicles' => $vehicles,
                'categories' => $categories,
                'search' => $request->search,
                'value' => $request->value ?? 'all'
            ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(): Renderable
    {
        $this->authorize('vehicle_add');

        return view('vehiclemanagement::admin.vehicle.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('vehicle_add');

        $validated = $request->validate([
            'brand_id' => 'required',
            'model_id' => 'required',
            'category_id' => 'required',
            'licence_plate_number' => 'required',
            'licence_expire_date' => 'required|date',
            'vin_number' => 'required',
            'transmission' => 'required',
            'fuel_type' => 'required',
            'ownership' => 'required|in:admin,driver',
            'upload_documents' => 'array',
            'upload_documents.*' => 'required|mimes:xls,xlsx,pdf,png,jpeg,cvc,csv,jpg|max:10000',
            'driver_id' => 'required|unique:vehicles,driver_id',
        ]);

        $this->vehicle->store(attributes: $validated);

        Toastr::success(ucfirst(VEHICLE_CREATE_200['message']));
        return redirect()->route('admin.vehicle.index');
    }

    /**
     * Show the specified resource.
     * @param string $id
     * @return Renderable
     */
    public function show(string $id): Renderable
    {
        $this->authorize('vehicle_view');

        $relations = ['brand', 'model', 'category', 'driver'];
        $vehicle = $this->vehicle->getBy(column: 'id', value: $id, attributes: $relations);
        return view('vehiclemanagement::admin.vehicle.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param string $id
     * @return Renderable
     */
    public function edit(string $id): Renderable
    {
        $this->authorize('vehicle_edit');

        $relations = ['brand', 'model', 'category', 'driver'];
        $vehicle = $this->vehicle->getBy(column: 'id', value: $id, attributes: $relations);
        return view('vehiclemanagement::admin.vehicle.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $this->authorize('vehicle_edit');

        $request->validate([
            'brand_id' => 'required',
            'model_id' => 'required',
            'category_id' => 'required',
            'licence_plate_number' => 'required',
            'licence_expire_date' => 'required|date',
            'vin_number' => 'required',
            'transmission' => 'required',
            'fuel_type' => 'required',
            'ownership' => 'required|in:admin,driver',
            'upload_documents' => 'array',
            'upload_documents.*' => 'nullable|mimes:xls,xlsx,pdf,png,jpeg,csv,jpg|max:10000',
            'driver_id' => 'required',
        ]);

        $this->vehicle->update($request->all(), $id);

        Toastr::success(VEHICLE_UPDATE_200['message']);
        return redirect()->route('admin.vehicle.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->authorize('vehicle_delete');

        $this->vehicle->destroy(id: $id);

        Toastr::success(DEFAULT_DELETE_200['message']);
        return redirect()->route('admin.vehicle.index');
    }

    /**
     * Update the status specified resource in storage.
     * @param Request $request
     * @return JsonResponse
     */

    public function status(Request $request): JsonResponse
    {
        $this->authorize('vehicle_edit');

        $validated = $request->validate([
            'status' => 'boolean'
        ]);
        $model = $this->vehicle->update(attributes: $validated, id: $request->id);

        $push = getNotification('vehicle_approved');
        if ($request->status && $model->driver->fcm_token) {
            sendDeviceNotification(
                fcm_token: $model->driver->fcm_token,
                title: translate($push['title']),
                description: translate($push['description']),
                action: 'account_approved',
                user_id: $model->driver->id
            );
        }
        return response()->json($model);
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|Response|string|StreamedResponse
     */
    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('vehicle_export');

        $attributes = [
            'relations' => ['level'],
            'query' => $request['query'],
            'value' => $request['value'],
        ];

        !is_null($request['search']) ? $attributes['search'] = $request['search'] : '';

        $vehicle = $this->vehicle->get(limit: 9999999999999999, offset: 1, attributes: $attributes, relations: ['category', 'model', 'brand']);
        $data = $vehicle->map(function ($item) {

            return [
                'id' => $item['id'],
                'brand' => $item->brand->name,
                'model' => $item->model->name,
                'vin' => $item['vin_number'],
                'license' => $item['licence_plate_number'],
                'owner' => $item['ownership'],
                'seat_capacity' => $item->model->seat_capacity,
                "hatch_bag_capacity" => $item->model->hatch_bag_capacity,
                "fuel" => $item['fuel_type'],
                "mileage" => $item->model->engine,
                "is_active" => $item['is_active'],
                "created_at" => $item['created_at'],
            ];
        });

        return exportData($data, $request['file'], 'vehiclemanagement::admin.vehicle.print');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|Response|string|StreamedResponse
     */
    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $request->merge([
            'logable_type' => 'Modules\VehicleManagement\Entities\Vehicle',
        ]);
        return log_viewer($request->all());
    }


    /**
     * @param Request $request
     * @return View
     */
    public function trashed(Request $request): View
    {
        $this->authorize('super-admin');

        $search = $request->has('search') ? $request->search : null;
        $vehicles = $this->vehicle->trashed(['search' => $search]);

        return view('vehiclemanagement::admin.vehicle.trashed', compact('vehicles', 'search'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');

        $this->vehicle->restore($id);

        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.vehicle.index');

    }

    public function permanentDelete($id){
        $this->authorize('super-admin');
        $this->vehicle->permanentDelete(id: $id);
        Toastr::success(DEFAULT_DELETE_200['message']);
        return back();
    }

}

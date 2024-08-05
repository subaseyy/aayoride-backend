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
use Illuminate\Validation\Rule;
use Modules\VehicleManagement\Repositories\VehicleModelRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VehicleModelController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private VehicleModelRepository $model)
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
        $relations = ['vehicles'];
        $models = $this->model->get(limit: paginationLimit(), offset: 1, attributes: $validated, relations: $relations);

        return view('vehiclemanagement::admin.model.index',
            [
                'models' => $models,
                'search' => $request->search,
                'value' => $request->value ?? 'all'
            ]
        );
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
            'model_name' => ['required', Rule::unique('vehicleModels', 'name')->where(fn($query) => $query->where('brand_id', $request->brand_id))],
            'brand_id' => 'required',
            'short_desc' => 'required',
            'model_image' => 'required|image|mimes:png|max:5000',
            'seat_capacity' => 'numeric|gt:0',
            'maximum_weight' => 'numeric|gt:0',
            'hatch_bag_capacity' => 'numeric|gt:0',
            'engine' => 'required'
        ]);

        $this->model->store(attributes: $validated);

        Toastr::success(MODEL_CREATE_200['message']);
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param string $id
     * @return Renderable
     */
    public function edit(string $id): Renderable
    {
        $this->authorize('vehicle_edit');

        $relations = ['brand', 'vehicles'];
        $model = $this->model->getBy(column: 'id', value: $id, attributes: $relations);

        return view('vehiclemanagement::admin.model.edit', compact('model'));
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

        $validated = $request->validate([
            'model_name' => 'required|unique:vehicle_models,name,' . $id,
            'short_desc' => 'required',
            'model_image' => 'image|mimes:png|max:5000',
            'seat_capacity' => 'numeric|gt:0',
            'maximum_weight' => 'numeric|gt:0',
            'hatch_bag_capacity' => 'numeric|gt:0',
            'engine' => 'required',
            'brand_id' => 'required|exists:vehicle_brands,id'
        ]);

        $this->model->update(attributes: $validated, id: $id);

        Toastr::success(MODEL_UPDATE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.model.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->authorize('vehicle_delete');

        $this->model->destroy(id: $id);

        Toastr::success(DEFAULT_DELETE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.model.index');
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
        $model = $this->model->update(attributes: $validated, id: $request->id);

        return response()->json($model);
    }

    /**
     * Ajax call for all models
     * @param Request $request
     * @param $brand_id
     * @return JsonResponse
     */
    public function ajax_models(Request $request, $brand_id): JsonResponse
    {

        $attributes = ['query' => 'brand_id', 'value' => $brand_id];
        $models = $this->model->get(limit: 1000, offset: 1, dynamic_page: true, attributes: $attributes);

        return response()->json([
            'template' => view('vehiclemanagement::admin.partials._model-selector', compact('models'))->render(),
        ]);
    }

    /**
     * Ajax call for child models
     * @param Request $request
     * @param $brand_id
     * @return JsonResponse
     */
    public function ajax_models_child(Request $request, $brand_id): JsonResponse
    {
        $attributes = ['query' => 'brand_id', 'value' => $brand_id];
        $models = $this->model->get(limit: 1000, offset: 1, dynamic_page: true, attributes: $attributes);;
        $model_id = $request->model_id ?? null;

        return response()->json([
            'template' => view('vehiclemanagement::admin.partials._model-selector', compact('models', 'model_id'))->render()
        ]);
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

        $model = $this->model->get(limit: 9999999999999999, offset: 1, attributes: $attributes, relations: ['vehicles']);
        $data = $model->map(function ($item) {

            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'description' => $item['description'],
                'brand_id' => $item['brand_id'],
                'seat_capacity' => $item['seat_capacity'],
                'maximum_weight' => $item['maximum_weight'],
                "hatch_bag_capacity" => $item['hatch_bag_capacity'],
                "engine" => $item['engine'],
                "total_vehicles" => $item->vehicles->count(),
                "is_active" => $item['is_active'],
                "created_at" => $item['created_at'],
            ];
        });
        return exportData($data, $request['file'], 'vehiclemanagement::admin.model.print');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|Response|string|StreamedResponse
     */
    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('vehicle_log');

        $request->merge([
            'logable_type' => 'Modules\VehicleManagement\Entities\VehicleModel',
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
        $models = $this->model->trashed(['search' => $search]);

        return view('vehiclemanagement::admin.model.trashed', compact('models', 'search'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');

        $this->model->restore($id);

        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.model.index');

    }

    public function permanentDelete($id){
        $this->authorize('super-admin');
        $this->model->permanentDelete(id: $id);
        Toastr::success(DEFAULT_DELETE_200['message']);
        return back();
    }

}

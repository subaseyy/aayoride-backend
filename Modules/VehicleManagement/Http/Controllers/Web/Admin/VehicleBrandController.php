<?php

namespace Modules\VehicleManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\VehicleManagement\Interfaces\VehicleBrandInterface;
use Modules\VehicleManagement\Repositories\VehicleBrandRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VehicleBrandController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private VehicleBrandRepository $brand)
    {
    }

    /**
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

        $brands = $this->brand->get(limit: paginationLimit(), offset: 1, attributes: $validated, relations: ['vehicles']);

        return view('vehiclemanagement::admin.brand.index', [
            'brands' => $brands,
            'value' => $request->value ?? 'all',
            'search' => $request->search
        ]);
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
            'brand_name' => 'required|unique:vehicle_brands,name',
            'short_desc' => 'required',
            'brand_logo' => 'required|image|mimes:png|max:5000'
        ]);

        $this->brand->store(attributes: $validated);

        Toastr::success(ucfirst(BRAND_CREATE_200['message']));
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

        $brand = $this->brand->getBy(column: 'id', value: $id);
        return view('vehiclemanagement::admin.brand.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->authorize('vehicle_edit');

        $validated = $request->validate([
            'brand_name' => ['required', Rule::unique('vehicle_categories', 'name')->ignore($id)],
            'short_desc' => 'required',
            'brand_logo' => 'image|mimes:png|max:5000'
        ]);

        $this->brand->update(attributes: $validated, id: $id);

        Toastr::success(BRAND_UPDATE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.brand.index');

    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->authorize('vehicle_delete');

        $this->brand->destroy(id: $id);

        Toastr::success(BRAND_DELETE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.brand.index');
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
        $model = $this->brand->update(attributes: $validated, id: $request->id);

        return response()->json($model);
    }

    /**
     * Display a listing of the resource Ajax Select2.
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllAjax(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => 'sometimes'
        ]);

        $brands = $this->brand->get(limit: 1000, offset: 1, dynamic_page: true, attributes: $validated);

        $selectBrands = $brands->map(function ($items, $key) {
            return [
                'text' => $items->name,
                'id' => $items->id
            ];
        });
        return response()->json($selectBrands);
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|Response|string|StreamedResponse
     */
    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('vehicle_export');

        $attributes = [
            'query' => $request['query'],
            'value' => $request['value'],
        ];

        $relations = ['vehicles'];

        !is_null($request['search']) ? $attributes['search'] = $request['search'] : '';

        $brand = $this->brand->get(limit: 9999999999999999, offset: 1, attributes: $attributes, relations: $relations);
        $data = $brand->map(function ($item) {

            return [
                'id' => $item['id'],
                'brand_name' => $item['name'],
                'description' => $item['description'],
                'total_vehicles' => $item->vehicles->count(),
                'status' => $item['is_active'] ? 'active' : 'inactive',
                'created_at' => $item['created_at'],

            ];
        });
        return exportData($data, $request['file'], 'vehiclemanagement::admin.brand.print');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|Response|string|StreamedResponse
     */
    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('vehicle_log');

        $request->merge([
            'logable_type' => 'Modules\VehicleManagement\Entities\VehicleBrand',
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
        $brands = $this->brand->trashed(['search' => $search]);

        return view('vehiclemanagement::admin.brand.trashed', compact('brands', 'search'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');

        $this->brand->restore($id);

        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.brand.index');

    }

    public function permanentDelete($id){
        $this->authorize('super-admin');
        $this->brand->permanentDelete(id: $id);
        Toastr::success(BRAND_DELETE_200['message']);
        return back();
    }

}

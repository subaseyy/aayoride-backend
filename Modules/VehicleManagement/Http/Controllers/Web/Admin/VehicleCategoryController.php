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
use Modules\FareManagement\Repositories\TripFareRepository;
use Modules\VehicleManagement\Interfaces\VehicleCategoryInterface;
use Modules\VehicleManagement\Repositories\VehicleCategoryRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VehicleCategoryController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private VehicleCategoryRepository $category,
        private TripFareRepository $tripFare)
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
        $categories = $this->category->get(limit: paginationLimit(), offset: 1, attributes: $validated, relations: $relations);

        return view('vehiclemanagement::admin.category.index', [
            'categories' => $categories,
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
            'category_name' => 'required|unique:vehicle_categories,name',
            'short_desc' => 'required',
            'type' => 'required|in:car,motor_bike',
            'category_image' => 'required|image|mimes:png|max:5000'
        ]);

        $this->category->store(attributes: $validated);

        Toastr::success(CATEGORY_CREATE_200['message']);
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

        $category = $this->category->getBy(column: 'id', value: $id);
        return view('vehiclemanagement::admin.category.edit', compact('category'));
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
            'category_name' => ['required', Rule::unique('vehicle_categories', 'name')->ignore($id)],
            'short_desc' => 'required',
            'type' => 'required|in:car,motor_bike',
            'category_image' => 'image|mimes:png|max:5000'
        ]);

        $this->category->update(attributes: $validated, id: $id);

        Toastr::success(CATEGORY_UPDATE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.category.index');

    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->authorize('vehicle_delete');

        $this->category->destroy(id: $id);

        Toastr::success(DEFAULT_DELETE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.category.index');
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
        $model = $this->category->update(attributes: $validated, id: $request->id);

        if (!$request['status']) {
            $this->tripFare->delete($request->id);
        }
        return response()->json($model);
    }


    /**
     * Display a listing of the resource Ajax.
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllAjax(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => 'sometimes'
        ]);
        $category = $this->category->get(limit: 1000, offset: 1, dynamic_page: true, attributes: $validated);

        $selectCategories = $category->map(function ($items, $key) {
            return [
                'text' => $items->name,
                'id' => $items->id
            ];
        });
        return response()->json($selectCategories);
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

        $category = $this->category->get(limit: 9999999999999999, offset: 1, attributes: $attributes, relations: ['vehicles']);
        $data = $category->map(function ($item) {

            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'description' => $item['description'],
                'image' => $item['image'],
                'type' => $item['type'],
                "total_vehicles" => $item->vehicles->count(),
                "is_active" => $item['is_active'],
                "created_at" => $item['created_at'],
            ];
        });
        return exportData($data, $request['file'], 'vehiclemanagement::admin.category.print');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|Response|string|StreamedResponse
     */
    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('vehicle_log');

        $request->merge([
            'logable_type' => 'Modules\VehicleManagement\Entities\VehicleCategory',
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
        $categories = $this->category->trashed(['search' => $search]);

        return view('vehiclemanagement::admin.category.trashed', compact('categories', 'search'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');

        $this->category->restore($id);

        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.vehicle.attribute-setup.category.index');

    }

    public function permanentDelete($id){
        $this->authorize('super-admin');
        $this->category->permanentDelete(id: $id);
        Toastr::success(DEFAULT_DELETE_200['message']);
        return back();
    }
}

<?php

namespace Modules\ParcelManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\BusinessManagement\Interfaces\BusinessSettingInterface;
use Modules\ParcelManagement\Interfaces\ParcelWeightInterface;
use Rap2hpoutre\FastExcel\FastExcel;


class ParcelWeightController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private ParcelWeightInterface $weight,
        private BusinessSettingInterface $businessInfo)
    {
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $this->authorize('parcel_view');

        $validated = $request->validate([
            'value' => 'in:all,active,inactive',
            'query' => 'sometimes',
            'search' => 'sometimes'
        ]);

        $weights = $this->weight->get(limit: paginationLimit(), offset: 1, attributes: $validated);
        $weightUnit = $this->businessInfo->getBy(column:"dummy", value:1, attributes:['key_name' => 'parcel_weight_unit', 'settings_type' => 'business_information']);
        return view('parcelmanagement::admin.attributes.weight.index', [
            'weights' => $weights,
            'weightUnit' => $weightUnit,
            'value' => $request->value??'all',
            'search' => $request->search
        ]);
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

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('parcel_add');

        $validatedData = $request->validate([
            'minimum_weight' => 'required|numeric|lte:maximum_weight|gte:0',
            'maximum_weight' => 'required|numeric|gt:minimum_weight',
        ]);

        $weight_lists = $this->weight->get(limit:100, offset:1);

        foreach ($weight_lists as $list) {
            if($request->minimum_weight<=$list->max_weight && $request->maximum_weight >= $list->min_weight){
                Toastr::error(ucfirst(PARCEL_WEIGHT_EXISTS_403['message']));
                return back();
            }
        }

        $this->weight->store(attributes:$validatedData);
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

        $weight = $this->weight->getBy(column:'id', value:$id);
        $weightUnit = $this->businessInfo->getBy(column:"dummy", value:1, attributes:['key_name' => 'parcel_weight_unit', 'settings_type' => 'business_information']);
        return view('parcelmanagement::admin.attributes.weight.edit', compact('weight', 'weightUnit'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->authorize('parcel_edit');

        $validatedData = $request->validate([
            'minimum_weight' => 'required|numeric|lte:maximum_weight',
            'maximum_weight' => 'required|numeric|gte:minimum_weight',
        ]);

        $weight_lists = $this->weight->get(limit:100, offset:1, except:[$id]);
        foreach ($weight_lists as $list) {
            if($list->max_weight >= $request->minimum_weight && $list->min_weight <= $request->maximum_weight){

                Toastr::error(ucfirst(PARCEL_WEIGHT_EXISTS_403['message']));
                return back();
            }
        }

        $this->weight->update(attributes:$validatedData, id:$id);

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

        $this->weight->destroy(id: $id);

        Toastr::success(PARCEL_WEIGHT_DESTROY_200['message']);
        return redirect()->route('admin.parcel.attribute.weight.index');
    }

    /**
     * Update the status specified resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function status(Request $request):JsonResponse
    {
        $this->authorize('parcel_edit');

        $validated = $request->validate([
            'status' => 'boolean'
        ]);
       $model = $this->weight->update(attributes:$validated, id:$request->id);

       return response()->json($model);
    }

    /**
     * Download the  specified resource in storage.
     * @param Request $request
     * @return mixed
     */
    public function download(Request $request):mixed
    {
        $this->authorize('parcel_export');


        $attributes = [
            'relations' => ['level'],
            'query' => $request['query'],
            'value' => $request['value'],
        ];

        !is_null($request['search'])? $attributes['search'] = $request['search'] : '';

        $roles = $this->weight->get(limit: 9999999999999999, offset: 1, attributes: $attributes);
        $data = $roles->map(function ($item){

            return [
                'id' => $item['id'],
                'weight_range' => $item['min_weight']. '-' . $item['max_weight'] . 'Kg',
                'status' => $item['is_active'] ? 'Active' : 'Inactive',
            ];
        });

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
     * @return View
     */
    public function trashed(Request $request): View
    {
        $this->authorize('super-admin');

        $search = $request->has('search') ? $request->search : null;
        $weights = $this->weight->trashed(['search' => $search]);
        $weightUnit = $this->businessInfo->getBy(column:"dummy", value:1, attributes:['key_name' => 'parcel_weight_unit', 'settings_type' => 'business_information']);

        return view('parcelmanagement::admin.attributes.weight.trashed', compact('weights', 'search', 'weightUnit'));

    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');

        $this->weight->restore($id);

        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.parcel.attribute.weight.index');
    }

    public function permanentDelete($id){
        $this->authorize('super-admin');
        $this->weight->permanentDelete(id: $id);
        Toastr::success(PARCEL_WEIGHT_DESTROY_200['message']);
        return back();
    }
}


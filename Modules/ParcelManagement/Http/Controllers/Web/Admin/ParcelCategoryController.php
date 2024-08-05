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
use Illuminate\Validation\Rule;
use Modules\ParcelManagement\Interfaces\ParcelCategoryInterface;
use Rap2hpoutre\FastExcel\FastExcel;

class ParcelCategoryController extends Controller
{
    use AuthorizesRequests;
    private ParcelCategoryInterface $category;

    public function __construct(ParcelCategoryInterface $category)
    {
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Renderable
     */
    public function index(Request $request): Renderable
    {
        $this->authorize('parcel_view');

        $validated = $request->validate([
            'value' => 'in:all,active,inactive',
            'query' => 'sometimes',
            'search' => 'sometimes'
        ]);

        $categories = $this->category->getCategorizedParcels(limit: paginationLimit(), offset: 1, status_column:'completed' , attributes: $validated);
        return view('parcelmanagement::admin.attributes.category.index', [
            'categories' => $categories,
            'value' => $request->value??'all',
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
        $this->authorize('parcel_add');

        $validatedData = $request->validate([
            'category_name' => 'required|unique:parcel_categories,name',
            'short_desc' => 'required',
            'category_icon' => 'required|image|mimes:png|max:5000'
        ]);

        $this->category->store(attributes:$validatedData);

        Toastr::success(ucfirst(PARCEL_CATEGORY_STORE_200['message']));
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

        $category = $this->category->getBy(column:'id', value:$id);
        return view('parcelmanagement::admin.attributes.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->authorize('parcel_edit');

        $validated = $request->validate([
            'category_name' =>  ['required', Rule::unique('parcel_categories', 'name')->ignore($id)],
            'short_desc' => 'required',
            'category_icon' => 'image|mimes:png|max:5000'
        ]);

        $this->category->update(attributes:$validated, id:$id);

        Toastr::success(PARCEL_CATEGORY_UPDATE_200['message']);
        return redirect()->route('admin.parcel.attribute.category.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $this->authorize('parcel_delete');

        $this->category->destroy(id: $id);

        Toastr::success(PARCEL_CATEGORY_DESTROY_200['message']);
        return redirect()->route('admin.parcel.attribute.category.index');
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
       $model = $this->category->update(attributes:$validated, id:$request->id);

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

        $roles = $this->category->get(limit: 9999999999999999, offset: 1, attributes: $attributes, relations: ['parcels']);
        $data = $roles->map(function ($item){

            return [
                'id' => $item['id'],
                'parcel_category_name' => $item['name'],
                'total_delivered' => $item['parcels']->count(),
                'status' => $item['is_active'] ? 'Active' : 'Inactive',
            ];
        });

        return exportData($data, $request['file'], 'parcelmanagement::admin.attributes.category.print');
    }

    public function log(Request $request)
    {
        $this->authorize('parcel_log');

        $request->merge([
            'logable_type' => 'Modules\ParcelManagement\Entities\ParcelCategory',
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
        $categories = $this->category->trashed(['search' => $search, 'relations' => ['parcels']]);

        return view('parcelmanagement::admin.attributes.category.trashed', compact('categories', 'search'));

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
        return redirect()->route('admin.parcel.attribute.category.index');

    }

    public function permanentDelete($id){
        $this->authorize('super-admin');
        $this->category->permanentDelete(id: $id);
        Toastr::success(PARCEL_CATEGORY_DESTROY_200['message']);
        return back();
    }
}

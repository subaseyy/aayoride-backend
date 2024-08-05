<?php

namespace Modules\PromotionManagement\Http\Controllers\Web\New\Admin;

use App\Http\Controllers\BaseController;
use App\Service\BaseServiceInterface;
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
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\PromotionManagement\Http\Requests\BannerSetupStoreUpdateRequest;
use Modules\PromotionManagement\Service\Interface\BannerSetupServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BannerSetupController extends BaseController
{
    use AuthorizesRequests;

    public function __construct(BannerSetupServiceInterface $baseService)
    {
        parent::__construct($baseService);
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('promotion_view');
        $banners = $this->baseService->index(criteria: $request?->all(), orderBy: ['created_at' => 'desc'], limit: paginationLimit(), offset: $request['page']??1);
        return view('promotionmanagement::admin.banner-setup.index', compact('banners'));
    }

    public function store(BannerSetupStoreUpdateRequest $request)
    {
        $this->authorize('promotion_add');
        $this->baseService->create(data: $request->validated());
        Toastr::success(BANNER_STORE_200['message']);
        return back();
    }

    public function edit($id)
    {
        $this->authorize('promotion_edit');
        $banner = $this->baseService->findOne(id: $id);
        return view('promotionmanagement::admin.banner-setup.edit', compact('banner'));
    }

    public function update(BannerSetupStoreUpdateRequest $request, $id)
    {
        $this->authorize('promotion_edit');
        $this->baseService->update(id: $id, data: $request->validated());
        Toastr::success(BANNER_UPDATE_200['message']);
        return back();

    }

    public function destroy($id)
    {
        $this->authorize('promotion_delete');
        $this->baseService->delete(id: $id);
        Toastr::success(BANNER_DESTROY_200['message']);
        return back();
    }

    public function status(Request $request): JsonResponse
    {
        $this->authorize('promotion_edit');
        $request->validate([
            'status' => 'boolean'
        ]);
        $model = $this->baseService->statusChange(id: $request->id, data: $request->all());
        return response()->json($model);
    }


    public function trashed(Request $request): View
    {
        $this->authorize('super-admin');
        $banners = $this->baseService->trashedData(criteria: $request->all(), limit: paginationLimit(), offset: $request['page']??1);
        return view('promotionmanagement::admin.banner-setup.trashed', compact('banners'));
    }

    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');

        $this->baseService->restoreData(id: $id);

        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.promotion.banner-setup.index');

    }

    public function permanentDelete($id)
    {
        $this->authorize('super-admin');
        $this->baseService->permanentDelete(id: $id);
        Toastr::success(BANNER_DESTROY_200['message']);
        return back();
    }

    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('promotion_export');
        $banner = $this->baseService->getBy(criteria: $request->all());
        $data = $banner->map(function ($item) {
            return [
                'id' => $item['id'],
                'banner_title' => $item['name'],
                "image" => $item['image'],
                'position' => $item['display_position'],
                'redirect_link' => $item['redirect_link'],
                "total_redirection" => $item['total_redirection'],
                "group" => $item['banner_group'],
                'time_period' => $item['time_period'] == 'all_time' ? 'All Time' : $item['start_date'] . ' To ' . $item['end_date'],
                "is_active" => $item['is_active'],
                "created_at" => $item['created_at'],
            ];
        });

        return exportData($data, $request['file'], 'promotionmanagement::admin.banner-setup.print');
    }


    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('promotion_log');

        $request->merge([
            'logable_type' => 'Modules\PromotionManagement\Entities\BannerSetup',
        ]);
        return log_viewer($request->all());
    }
}

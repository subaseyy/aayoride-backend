<?php

namespace Modules\PromotionManagement\Http\Controllers\Web\New\Admin;

use App\Http\Controllers\BaseController;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
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
use Modules\PromotionManagement\Http\Requests\CouponSetupStoreUpdateRequest;
use Modules\PromotionManagement\Service\Interface\CouponSetupServiceInterface;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;
use Modules\UserManagement\Service\Interface\UserLevelServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CouponSetupController extends BaseController
{
    use AuthorizesRequests;

    protected $couponSetupService;
    protected $userLevelService;
    protected $tripRequestService;

    public function __construct(CouponSetupServiceInterface $couponSetupService, UserLevelServiceInterface $userLevelService, TripRequestServiceInterface $tripRequestService)
    {
        parent::__construct($couponSetupService);
        $this->couponSetupService = $couponSetupService;
        $this->userLevelService = $userLevelService;
        $this->tripRequestService = $tripRequestService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        //TODO
        $this->authorize('promotion_view');
        $dateRange = $request->query('date_range');
        $data = $request?->date_range;
        $this->couponSetupService->updatedBy(criteria: [['end_date', '<', Carbon::today()->endOfDay()]], data: ['is_active' => false]);
        $cardValues = $this->couponSetupService->getCardValues($data);
        $analytics = $this->tripRequestService->getAnalytics($data);
        $coupons = $this->couponSetupService->index(criteria: $request?->all(), orderBy: ['created_at' => 'desc'], limit: paginationLimit(), offset: $request['page'] ?? 1);

        return view('promotionmanagement::admin.coupon-setup.index', [
            'coupons' => $coupons,
            'cardValues' => $cardValues,
            'label' => $analytics[0],
            'data' => $analytics[1],
            'dateRangeValue' => $dateRange
        ]);
    }

    public function create(): Renderable
    {
        $this->authorize('promotion_add');
        $levels = $this->userLevelService->getBy(criteria: ['user_type' => CUSTOMER]);
        return view('promotionmanagement::admin.coupon-setup.create', compact('levels'));
    }

    public function store(CouponSetupStoreUpdateRequest $request): RedirectResponse
    {
        $this->authorize('promotion_add');
        if ($request->user_id == 'Select customer' && $request->user_level_id) {
            Toastr::error('please select customer or user level');
            return back();
        }
        if ($request->coupon_rules == 'area_wise' && !($request->areas)) {
            Toastr::error(DEFAULT_FAIL_200['message']);
            return redirect()->back();
        }
        if ($request->coupon_rules == 'vehicle_category_wise' && !($request->categories)) {
            Toastr::error(DEFAULT_FAIL_200['message']);
            return redirect()->back();
        }
        $this->couponSetupService->create(data: $request->validated());
        Toastr::success(COUPON_STORE_200['message']);
        return redirect()->route('admin.promotion.coupon-setup.index');
    }

    public function edit(string $id): Renderable
    {
        $this->authorize('promotion_edit');
        $relations = ['categories', 'customer', 'level'];
        $coupon = $this->couponSetupService->findOne(id: $id, relations: $relations);
        return view('promotionmanagement::admin.coupon-setup.edit', compact('coupon'));
    }

    public function update(CouponSetupStoreUpdateRequest $request, $id)
    {
        $this->authorize('promotion_edit');
        $this->couponSetupService->update(id: $id, data: $request->validated());
        Toastr::success(COUPON_UPDATE_200['message']);
        return redirect()->route('admin.promotion.coupon-setup.index');
    }

    public function destroy($id)
    {
        $this->authorize('promotion_view');
        $this->couponSetupService->delete(id: $id);
        Toastr::success(COUPON_DESTROY_200['message']);
        return back();
    }

    public function status(Request $request): JsonResponse
    {
        $this->authorize('promotion_edit');
        $request->validate([
            'status' => 'boolean'
        ]);
        $model = $this->couponSetupService->statusChange(id: $request->id, data: $request->all());
        return response()->json($model);
    }

    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('promotion_export');
        $coupon = $this->couponSetupService->index(criteria: $request->all(), orderBy: ['created_at' => 'desc']);

        $date = Carbon::now()->startOfDay();


        $data = $coupon->map(function ($item) use ($date) {

            if ($date->gt($item['end_date'])) {
                $couponStatus = ucwords(EXPIRED);
            } elseif (!$item['is_active']) {
                $couponStatus = ucwords(CURRENTLY_OFF);
            } elseif ($date->lt($item['start_date'])) {
                $couponStatus = ucwords(UPCOMING);
            } elseif ($date->lte($item['end_date'])) {
                $couponStatus = ucwords(RUNNING);
            } else {
                $couponStatus = ucwords(UPCOMING);
            }

            return [
                'id' => $item['id'],
                'Name' => $item['name'],
                'Description' => $item['description'],
                'User Id' => $item['user_id'],
                'User Level Id' => $item['user_level_id'] ?? '-',
                'Min Trip Amount' => getCurrencyFormat($item['min_trip_amount'] ?? 0),
                "Max Coupon Amount" => getCurrencyFormat($item['max_coupon_amount'] ?? 0),
                "Coupon" => getCurrencyFormat($item['coupon'] ?? 0),
                "Amount Type" => ucwords($item['amount_type']),
                "Coupon Type" => ucwords($item['coupon_type']),
                "Coupon Code" => $item['coupon_code'],
                "Limit" => $item['limit'],
                "Start Date" => $item['start_date'],
                "End Date" => $item['end_date'],
                "Rules" => ucwords($item['rules']),
                "Total Used" => $item['total_used'],
                "Total Amount" => getCurrencyFormat($item['total_amount'] ?? 0),
                "Duration In Days" => $item['start_date'] && $item['end_date'] ? Carbon::parse($item['end_date'])->diffInDays($item['start_date']) . ' days' : '-',
                "Avg Amount" => set_currency_symbol(round($item['total_used'] > 0 ? ($item['total_amount'] / $item['total_used']) : 0, 2)),
                "Coupon Status" => $couponStatus,
                "Active Status" => $item['is_active'] == 1 ? "Active" : "Inactive",
                "Created At" => $item['created_at'],
            ];
        });
        return exportData($data, $request['file'], 'promotionmanagement::admin.coupon-setup.print');
    }

    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('promotion_log');
        $request->merge(['logable_type' => 'Modules\PromotionManagement\Entities\CouponSetup']);
        return log_viewer($request->all());
    }

    public function trashed(Request $request): View
    {
        $this->authorize('super-admin');
        $coupons = $this->couponSetupService->trashedData(criteria: $request->all(), limit: paginationLimit(), offset: $request['page'] ?? 1);
        return view('promotionmanagement::admin.coupon-setup.trashed', compact('coupons'));
    }

    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');
        $this->couponSetupService->restoreData($id);
        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.promotion.coupon-setup.index');
    }

    public function permanentDelete($id)
    {
        $this->authorize('super-admin');
        $this->couponSetupService->permanentDelete(id: $id);
        Toastr::success(COUPON_DESTROY_200['message']);
        return back();
    }
}

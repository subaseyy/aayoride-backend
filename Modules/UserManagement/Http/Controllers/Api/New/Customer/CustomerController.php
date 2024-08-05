<?php

namespace Modules\UserManagement\Http\Controllers\Api\New\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\PromotionManagement\Service\Interface\AppliedCouponServiceInterface;
use Modules\PromotionManagement\Service\Interface\CouponSetupServiceInterface;
use Modules\UserManagement\Http\Requests\CustomerProfileUpdateApiRequest;
use Modules\UserManagement\Service\Interface\CustomerServiceInterface;
use Modules\UserManagement\Transformers\CustomerResource;

class CustomerController extends Controller
{
    protected $customerService;
    protected $couponSetupService;
    protected $appliedCouponService;

    public function __construct(CustomerServiceInterface $customerService, CouponSetupServiceInterface $couponSetupService, AppliedCouponServiceInterface $appliedCouponService)
    {
        $this->customerService = $customerService;
        $this->couponSetupService = $couponSetupService;
        $this->appliedCouponService = $appliedCouponService;
    }

    public function profileInfo(Request $request): JsonResponse
    {
        if ($request->user()->user_type == CUSTOMER) {
            $withAvgRelations = [['receivedReviews', 'rating']];
            $customer = $this->customerService->findOne(id: auth()->id(), withAvgRelations: $withAvgRelations, relations: ['userAccount', 'level'], withCountQuery: ['customerTrips' => []]);
            $customer = new CustomerResource($customer);
            return response()->json(responseFormatter(DEFAULT_200, $customer), 200);
        }
        return response()->json(responseFormatter(DEFAULT_401), 401);
    }

    public function updateProfile(CustomerProfileUpdateApiRequest $request): JsonResponse
    {
        $this->customerService->update(id: $request->user()->id, data: $request->validated());
        return response()->json(responseFormatter(DEFAULT_UPDATE_200), 200);
    }

    public function applyCoupon(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'coupon_id' => 'required|exists:coupon_setups,id'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $coupon = $this->couponSetupService->findOne($request->coupon_id);
        if (!$coupon) {
            return response()->json(responseFormatter(constant: COUPON_404), 403);
        }
        $user = auth('api')->user();

        // Check if the user already has an applied coupon
        if ($user->appliedCoupon && $user->appliedCoupon->coupon_setup_id == $coupon?->id) {
            // Remove the previously applied coupon
            $user->appliedCoupon->delete();
            return response()->json(responseFormatter(COUPON_REMOVED_200), 200);
        } else {
            if ($user->appliedCoupon) {
                $user->appliedCoupon->delete();
            }
            $appliedCoupon = $this->appliedCouponService->create(data: [
                'user_id' => $user->id,
                'coupon_setup_id' => $coupon->id
            ]);
            return response()->json(responseFormatter(COUPON_APPLIED_200), 200);
        }
    }

    public function changeLanguage(Request $request): JsonResponse
    {
        if (auth('api')->user()) {
            $this->customerService->changeLanguage(id: auth('api')->user()->id, data: [
                'current_language_key' => $request->header('X-localization') ?? 'en'
            ]);
            return response()->json(responseFormatter(DEFAULT_200), 200);
        }
        return response()->json(responseFormatter(DEFAULT_404), 200);
    }

}

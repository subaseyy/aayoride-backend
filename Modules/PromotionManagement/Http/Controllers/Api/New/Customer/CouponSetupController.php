<?php

namespace Modules\PromotionManagement\Http\Controllers\Api\New\Customer;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PromotionManagement\Http\Requests\CuponSetupStoreApplyRequest;
use Modules\PromotionManagement\Service\AppliedCouponService;
use Modules\PromotionManagement\Service\Interface\AppliedCouponServiceInterface;
use Modules\PromotionManagement\Service\Interface\CouponSetupServiceInterface;
use Modules\PromotionManagement\Transformers\CouponResource;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;
use Modules\UserManagement\Service\Interface\UserLevelServiceInterface;

class CouponSetupController extends Controller
{


    protected $userLevelService;
    protected $tripRequestService;
    protected $couponService;
    protected $appliedCouponService;
    public function __construct(CouponSetupServiceInterface $couponService, UserLevelServiceInterface $userLevelService,
                                TripRequestServiceInterface $tripRequestService, AppliedCouponServiceInterface $appliedCouponService)
    {
        $this->couponService = $couponService;
        $this->userLevelService = $userLevelService;
        $this->tripRequestService = $tripRequestService;
        $this->appliedCouponService = $appliedCouponService;
    }
    public function list(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $criteria = [
            'user_id' => $user->id,
            'level_id' => $user->level->id,
            'is_active' => 1,
            'date' => date('Y-m-d')
        ];
        $coupons = $this->couponService->getUserCouponList(data: $criteria, limit: $request->limit, offset: $request->offset);

        $data = CouponResource::collection($coupons);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $data, limit: $request->limit, offset: $request->offset));
    }


    public function apply(CuponSetupStoreApplyRequest $request): JsonResponse
    {

        if (empty($request->header('zoneId'))) {

            return response()->json(responseFormatter(ZONE_404), 200);
        }

        $couponQuery = $this->couponService->findOneBy(criteria: ['coupon_code' =>  $request->coupon_code]);

        $coupon = CouponResource::make($couponQuery);
        $user = auth('api')->user();
        if (!$coupon->is_active) {
            return response()->json(responseFormatter(constant: DEFAULT_NOT_ACTIVE), 200);
        }

        $ruleValidation = $this->tripRequestService->couponRuleValidate($coupon, $request->pickup_coordinates, $request->vehicle_category_id);

        if (!is_null($ruleValidation)) {
            return response()->json(responseFormatter(constant: $ruleValidation), 200);
        }

        if ($coupon->coupon_type == 'first_order') {
            $total = $this->findOneBy(criteria: ['customer_id' => $user->id]);
            if ($total < $coupon->limit) {
                return response()->json(responseFormatter(constant: DEFAULT_200, content: $coupon), 200);
            }

            return response()->json(responseFormatter(constant: COUPON_USAGE_LIMIT_406, content: $coupon), 200); //Limite orer
        }
        if ($coupon->limit == null) {
            return response()->json(responseFormatter(constant: DEFAULT_200, content: $coupon), 200);
        }

        $attributes = [
            'customer_id' => $user->id,
           'coupon_id' => [$coupon->id],
            'type' => 'ride_request'
        ];
        $total = $this->getBy($attributes)->count();
        if ($total < $coupon->limit) {
            return response()->json(responseFormatter(constant: DEFAULT_200, content: $coupon), 200);
        }
        return response()->json(responseFormatter(constant: COUPON_USAGE_LIMIT_406, content: $coupon), 200); //Limite orer


    }

}

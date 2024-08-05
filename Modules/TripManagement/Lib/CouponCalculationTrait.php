<?php

namespace Modules\TripManagement\Lib;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\PromotionManagement\Entities\CouponSetup;
use Modules\PromotionManagement\Service\Interface\AppliedCouponServiceInterface;
use Modules\PromotionManagement\Service\Interface\CouponSetupServiceInterface;
use Modules\TripManagement\Entities\TripRequest;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;

trait CouponCalculationTrait
{
    public function getEstimatedCouponDiscount($user,$vechileCategoryId,$estimatedAmount)
    {
        $couponSetupService = app(CouponSetupServiceInterface::class);
        $appliedCouponService = app(AppliedCouponServiceInterface::class);
        $tripRequestService = app(TripRequestServiceInterface::class);

        $tripAmountWithoutVatTaxAndTips = $estimatedAmount;
        $getAppliedCoupon = $appliedCouponService->findOneBy(criteria: ['user_id' => $user->id]);
        if ($getAppliedCoupon){
            $couponCriteria = [
                'id' => $getAppliedCoupon->coupon_setup_id,
                'fare' => $tripAmountWithoutVatTaxAndTips,
                'date' => date('Y-m-d')
            ];
            $coupon = $couponSetupService->getAppliedCoupon(data: $couponCriteria);
            if ($coupon){
                $userAppliedCouponCountCriteria = [
                    'customer_id' => $user->id,
                    'coupon_id' => $getAppliedCoupon->coupon_setup_id
                ];
                $userAppliedCouponCount = $tripRequestService->getBy(criteria: $userAppliedCouponCountCriteria)->count();

                if ($userAppliedCouponCount >= $coupon->limit) {
                    // maximum time applied
                    return false;
                }
                if ($coupon->rules === 'vehicle_category_wise') {
                    if ($coupon->categories->contains($vechileCategoryId)) {
                        $discount = $this->getCouponAmount($coupon, $user, $tripAmountWithoutVatTaxAndTips);
                        if ($discount === 0) {
                            return false;
                        }
                    } else {
                        //invalid coupon
                        return false;
                    }
                } else {
                    $discount = $this->getCouponAmount($coupon, $user, $tripAmountWithoutVatTaxAndTips);
                    if ($discount === 0) {
                        //invalid coupon
                        return false;
                    }
                }
                return true;
            }
        }

        return false;

    }

    public function getCouponDiscount($user, $trip, $coupon)
    {
        $discount = 0;
        $message = DEFAULT_200;

        DB::beginTransaction();
        $coupon_apply_count = TripRequest::query()
            ->where(['customer_id' => $user->id, 'coupon_id' => $coupon->id])
            ->count();
        $tripAmountWithoutVatTaxAndTips = $trip->paid_fare - $trip->fee->tips - $trip->fee->vat_tax;
        if ($coupon_apply_count >= $coupon->limit) {
            // maximum time applied
            return [
                'discount' => $discount,
                'message' => COUPON_USAGE_LIMIT_406
            ];
        }

        if ($coupon->rules == 'vehicle_category_wise') {
            if ($coupon->categories->contains($trip->vehicle_category_id)) {
                $discount = $this->getCouponAmount($coupon, $user, $tripAmountWithoutVatTaxAndTips);
                if ($discount == 0) {
                    //invalid coupon
                    $message = COUPON_404;
                } else {
                    $this->updateCouponCount($coupon, $discount);
                }
            } else {
                //invalid coupon
                $message = COUPON_404;
            }
        } else {
            $discount = $this->getCouponAmount($coupon, $user, $tripAmountWithoutVatTaxAndTips);
            if ($discount == 0) {
                //invalid coupon
                $message = COUPON_404;
            } else {
                $this->updateCouponCount($coupon, $discount);
            }
        }
        DB::commit();

        return [
            'discount' => $discount,
            'message' => $message
        ];
    }
    public function getFinalCouponDiscount($user, $trip)
    {
        $couponSetupService = app(CouponSetupServiceInterface::class);
        $appliedCouponService = app(AppliedCouponServiceInterface::class);
        $tripRequestService = app(TripRequestServiceInterface::class);


        $discount = 0;
        $message = DEFAULT_200;

        $tripAmountWithoutVatTaxAndTips = $trip->paid_fare - $trip->fee->tips - $trip->fee->vat_tax;
        $getAppliedCoupon = $appliedCouponService->findOneBy(criteria: ['user_id' => $user->id]);
        if ($getAppliedCoupon){
            $couponCriteria = [
                'id' => $getAppliedCoupon->coupon_setup_id,
                'fare' => $tripAmountWithoutVatTaxAndTips,
                'date' => date('Y-m-d')
            ];
            $coupon = $couponSetupService->getAppliedCoupon(data: $couponCriteria);
            if ($coupon){
                $userAppliedCouponCountCriteria = [
                    'customer_id' => $user->id,
                    'coupon_id' => $getAppliedCoupon->coupon_setup_id
                ];
                $userAppliedCouponCount = $tripRequestService->getBy(criteria: $userAppliedCouponCountCriteria)->count();
                if ($userAppliedCouponCount >= $coupon->limit) {
                    // maximum time applied
                    return [
                        'coupon' => $coupon,
                        'coupon_id' => $coupon->id,
                        'discount' => $discount,
                        'message' => COUPON_USAGE_LIMIT_406
                    ];
                }

                if ($coupon->rules === 'vehicle_category_wise') {
                    if ($coupon->categories->contains($trip->vehicle_category_id)) {
                        $discount = $this->getCouponAmount($coupon, $user, $tripAmountWithoutVatTaxAndTips);
                        if ($discount == 0) {
                            //invalid coupon
                            $message = COUPON_404;
                        }
                    } else {
                        //invalid coupon
                        $message = COUPON_404;
                    }
                } else {
                    $discount = $this->getCouponAmount($coupon, $user, $tripAmountWithoutVatTaxAndTips);
                    if ($discount == 0) {
                        //invalid coupon
                        $message = COUPON_404;
                    }
                }

                return [
                    'coupon' => $coupon,
                    'coupon_id' => $coupon->id,
                    'discount' => $discount,
                    'message' => $message
                ];
            }
        }

        return [
            'coupon' => null,
            'coupon_id' => null,
            'discount' => 0,
            'message' => COUPON_404
        ];

    }

    private function getCouponAmount($coupon, $user, $tripAmountWithoutVatTaxAndTips)
    {
        if ($coupon->user_id == $user->id || $coupon->user_id == 'all' || $coupon->user_level_id == $user->user_level_id) {
            // apply coupon

            if ($coupon->amount_type == 'percentage') {
                $discount = ($coupon->coupon * $tripAmountWithoutVatTaxAndTips) / 100;
                //if calculated discount exceeds coupon max discount amount
                if ($discount > $coupon->max_coupon_amount) {
                    return round($coupon->max_coupon_amount, 2);
                }
                return round($discount, 2);
            }
            $amount = $tripAmountWithoutVatTaxAndTips;
            if ($coupon->coupon > $amount) {
                return round(min($coupon->coupon, $amount), 2);
            }
            return round($coupon->coupon);
        }
        //invalid coupon
        return 0;
    }


    private function updateCouponCount($coupon, $amount)
    {
        $coupon = CouponSetup::where('id', $coupon->id)->first();
        $coupon->total_amount += $amount;
        $coupon->increment('total_used');
        $coupon->save();

    }

}

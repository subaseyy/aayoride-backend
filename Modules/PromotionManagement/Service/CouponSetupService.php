<?php

namespace Modules\PromotionManagement\Service;

use App\Service\BaseService;
use Illuminate\Database\Eloquent\Model;
use Modules\PromotionManagement\Repository\CouponSetupRepositoryInterface;
use Modules\PromotionManagement\Service\Interface\CouponSetupServiceInterface;
use Modules\TripManagement\Repository\TripRequestRepositoryInterface;

class CouponSetupService extends BaseService implements Interface\CouponSetupServiceInterface
{
    protected $tripRequestRepository;
    protected $couponSetupRepository;

    public function __construct(CouponSetupRepositoryInterface $couponSetupRepository, TripRequestRepositoryInterface $tripRequestRepository)
    {
        parent::__construct($couponSetupRepository);
        $this->couponSetupRepository = $couponSetupRepository;
        $this->tripRequestRepository = $tripRequestRepository;

    }

    public function create(array $data): ?Model
    {
        $storeData = [
            'name' => $data['coupon_title'],
            'description' => $data['short_desc'],
            'user_id' => $data['user_id'] ?? null,
            'user_level_id' => $data['user_level_id'] ?? null,
            'min_trip_amount' => $data['coupon_type'] == 'first_trip' ? 0 : $data['minimum_trip_amount'],
            'max_coupon_amount' => $data['max_coupon_amount'] == null ? 0 : $data['max_coupon_amount'],
            'coupon' => $data['coupon'],
            'coupon_code' => $data['coupon_code'],
            'coupon_type' => $data['coupon_type'],
            'amount_type' => $data['amount_type'],
            'limit' => $data['limit_same_user'],
            'rules' => $data['coupon_rules'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];
        $coupon = parent::create($storeData);
         if (array_key_exists('categories', $data)) {
            $coupon?->categories()->attach($data['categories']);
         }
        return $coupon;
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        $model = $this->findOne(id: $id);
        $updateData = [
            'name' => $data['coupon_title'],
            'description' => $data['short_desc'],
            'min_trip_amount' => $data['coupon_type'] == 'first_trip' ? 0 : $data['minimum_trip_amount'],
            'max_coupon_amount' => $data['max_coupon_amount'] == null ? 0 : $data['max_coupon_amount'],
            'coupon' => $data['coupon'],
            'coupon_type' => $data['coupon_type'],
            'amount_type' => $data['amount_type'],
            'limit' => $data['limit_same_user'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];
        return parent::update($id, $updateData);
    }

    public function getCardValues($dateRange)
    {
        $coupon = $this->couponSetupRepository->fetchCouponDataCount(dateRange: $dateRange, status: 'active');
        $trip = $this->tripRequestRepository->fetchTripData(dateRange: $dateRange);
        return [
            'total_coupon_amount' => $trip->sum('coupon_amount'),
            'total_active' => $coupon,
        ];
    }

    public function getUserCouponList(array $data, $limit = null, $offset = null)
    {
        return $this->couponSetupRepository->getUserCouponList(data: $data, limit: $limit, offset: $offset);
    }

    public function getAppliedCoupon(array $data){
        return $this->couponSetupRepository->getAppliedCoupon($data);
    }


}

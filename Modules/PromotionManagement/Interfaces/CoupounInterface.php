<?php

namespace Modules\PromotionManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface CoupounInterface extends BaseRepositoryInterface
{
    /**
    * Download functionalities
    * @param array $attributes
    * @return mixed
    */
    public function download(array $attributes = []):mixed;

        /**
    * Analytics functionalities
    * @param string $dateRange
    * @return mixed
    */
    public function getAnalytics(string $dateRange):mixed;
    public function getCardValues(string $dateRange);

    public function getAppliedCoupon(array $attributes): mixed;

    public function removeCouponUsage(array $attributes) : mixed;

    public function trashed(array $attributes);

    public function restore(string $id);

    public function permanentDelete(string $id);

    public function userCouponList(array $attributes);

}

<?php

namespace Modules\PromotionManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\PromotionManagement\Entities\CouponSetup;
use Modules\PromotionManagement\Repository\CouponSetupRepositoryInterface;
use Modules\UserManagement\Entities\User;

class CouponSetupRepository extends BaseRepository implements CouponSetupRepositoryInterface
{
    protected $user;
    public function __construct(CouponSetup $model, User $user)
    {
        parent::__construct($model);
        $this->user = $user;
    }


    public function fetchCouponDataCount($dateRange, string $status = null): int
    {
        $model = $this->model;
        $startDate = $endDate = null;

        switch ($dateRange) {
            case "this_week":
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case "this_month":
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case "this_year":
                $startDate = Carbon::now()->firstOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case "today":
                $startDate = $endDate = Carbon::today();
                break;
            default:
                $businessStart = $this->user->where( ['user_type'=>'super-admin'])->first();
                $startDate = $businessStart->created_at != null ? Carbon::parse($businessStart?->created_at) : Carbon::parse('2023-11-01');
                $endDate = Carbon::today();
                break;
        }

        switch ($status) {
            case "active":
                $data = $model->where(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $endDate)
                        ->where('end_date', '>=', $startDate);
                })->where('is_active',1)->count();
                break;
            default:
                if ($startDate && $endDate) {
                    $data = $model->where(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $endDate)
                            ->where('end_date', '>=', $startDate);
                    })->where('is_active',0)->count();
                } else {
                    $data = 0; // Or handle default case based on your requirement
                }
                break;
        }

        return $data;
    }

    public function getUserCouponList(array $data, $limit = null, $offset = null)
    {
        $model = $this->model
            ->where(fn($query) => $query->where('user_id', 'all')
                ->orWhere('user_id', $data['user_id'])
                ->orWhere('user_level_id', $data['level_id']))
            ->where('is_active', $data['is_active'])
            ->whereDate('start_date', '<=', $data['date'])
            ->whereDate('end_date', '>=', $data['date']);
        if ($limit) {
            return $model->paginate(perPage: $limit, page: $offset);
        }
        return $model->get();
    }

    public function getAppliedCoupon(array $data){
        return $this->model
            ->where('id', $data['id'])
            ->where('min_trip_amount', '<=', $data['fare'])
            ->where('start_date', '<=', $data['date'])
            ->where('end_date', '>=', $data['date'])
            ->where('is_active',1)
            ->first();
    }
}

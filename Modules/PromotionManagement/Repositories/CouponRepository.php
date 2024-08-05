<?php

namespace Modules\PromotionManagement\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\PromotionManagement\Entities\CouponSetup;
use Modules\PromotionManagement\Interfaces\CoupounInterface;
use Modules\TripManagement\Entities\TripRequest;

class CouponRepository implements CoupounInterface
{

    public function __construct(private CouponSetup $coupon, private TripRequest $trip)
    {
    }


    /**
     * @param int $limit
     * @param int $offset
     * @param bool $dynamic_page
     * @param array $except
     * @param array $attributes
     * @param array $relations
     * @return LengthAwarePaginator|array|Collection
     */
    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {

        $search = $attributes['search'] ?? '';
        $value = $attributes['value'] ?? 'all';
        $column = $attributes['query'] ?? '';

        $ExtraColumn = $attributes['column_name'] ?? null;
        $ExtraColumnValue = $attributes['column_value'] ?? null;

        $dataByDate = $attributes['dataByDate'] ?? null;

        $queryParam = ['search' => $search, 'query' => $column, 'value' => $value];

        $query = $this->coupon
            ->query()
            ->when(!empty($relations[0]), function ($query) use ($relations) {
                $query->with($relations);
            })
            ->when(!empty($attributes['dates']), function ($query) use ($attributes) {
                $query->whereBetween('created_at', [$attributes['dates']['start'], $attributes['dates']['end']]);
            })
            ->when($search, function ($query) use ($attributes) {
                $keys = explode(' ', $attributes['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->where('name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($column && $value != 'all', function ($query) use ($column, $value) {

                return $query->where($column, ($value == 'active' ? 1 : ($value == 'inactive' ? 0 : $value)));
            })
            ->when($ExtraColumn && $ExtraColumnValue, function ($query) use ($ExtraColumn, $ExtraColumnValue) {
                $query->whereIn($ExtraColumn, $ExtraColumnValue);
            })
            ->when($dataByDate, function ($query) {
                $query->whereDate('end_date', '>=', date('Y-m-d'))->whereDate('start_date', '<=', date('Y-m-d'));
            })
            ->when(!empty($except[0]), function ($query) use ($except) {
                $query->whereNotIn('id', $except);
            });

        if (!$dynamic_page) {
            return $query->latest()->paginate(paginationLimit())->appends($queryParam);
        }

        return $query->latest()->paginate($limit, ['*'], 'page', $offset);
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return Model
     */
    public function getBy(string $column, int|string $value, array $attributes = []): Model
    {
        return $this->coupon
            ->when(!empty($attributes[0]), function ($q) use ($attributes) {
                $q->with($attributes);
            })
            ->where([$column => $value])->first();
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {
        $coupon = $this->coupon;
        $coupon->name = $attributes['coupon_title'];
        $coupon->description = $attributes['short_desc'];
        $coupon->user_id = $attributes['user_id'] ?? null;
        $coupon->user_level_id = $attributes['user_level_id'] ?? null;
        $coupon->min_trip_amount = $attributes['coupon_type'] == 'first_trip' ? 0 : $attributes['minimum_trip_amount'];
        $coupon->max_coupon_amount = $attributes['max_coupon_amount'] == null ? 0 : $attributes['max_coupon_amount'];
        $coupon->coupon = $attributes['coupon'];
        $coupon->coupon_code = $attributes['coupon_code'];
        $coupon->coupon_type = $attributes['coupon_type'];
        $coupon->amount_type = $attributes['amount_type'];
        $coupon->limit = $attributes['limit_same_user'];
        $coupon->start_date = $attributes['start_date'];
        $coupon->end_date = $attributes['end_date'];
        $coupon->rules = $attributes['coupon_rules'];
        $coupon->save();

        if (array_key_exists('categories', $attributes)) {
            $coupon->categories()->attach($attributes['categories']);
        }

        return $coupon;
    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model
    {
        $coupon = $this->getBy(column: 'id', value: $id);

        if (!array_key_exists('status', $attributes)) {
            $coupon->name = $attributes['coupon_title'];
            $coupon->description = $attributes['short_desc'];
            $coupon->min_trip_amount = $attributes['coupon_type'] == 'first_trip' ? 0 : $attributes['minimum_trip_amount'];
            $coupon->max_coupon_amount = $attributes['max_coupon_amount'] == null ? 0 : $attributes['max_coupon_amount'];
            $coupon->coupon = $attributes['coupon'];
            $coupon->coupon_type = $attributes['coupon_type'];
            $coupon->amount_type = $attributes['amount_type'];
            $coupon->limit = $attributes['limit_same_user'];
            $coupon->start_date = $attributes['start_date'];
            $coupon->end_date = $attributes['end_date'];
        } else {
            $coupon->is_active = $attributes['status'];
        }

        $coupon->save();
        return $coupon;
    }

    /**
     * @param string $id
     * @return Model
     */
    public function destroy(string $id): Model
    {
        $coupon = $this->getBy(column: 'id', value: $id);
        $coupon->delete();
        return $coupon;
    }

    /**
     * Download functionalities
     * @param array $attributes
     * @return mixed
     */
    public function download(array $attributes = []): mixed
    {
        $search = array_key_exists('search', $attributes) ? $attributes['search'] : '';
        $value = array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column = array_key_exists('query', $attributes) ? $attributes['query'] : '';

        return $this->coupon->select('*', DB::raw('DATEDIFF(end_date,start_date) AS duration_in_days'), DB::raw('IF(total_used>0,total_amount/total_used,0) as avg_amount'))
            ->when($search, function ($query) use ($attributes) {
                $keys = explode(' ', $attributes['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->where('name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($column && $value != 'all', function ($query) use ($column, $value) {
                return $query->where($column, ($value == 'active' ? 1 : ($value == 'inactive' ? 0 : $value)));
            })
            ->latest()
            ->get();

    }

    public function getAnalytics($dateRange): mixed
    {
        $months = array(
            '"Jan"',
            '"Feb"',
            '"Mar"',
            '"Apr"',
            '"May"',
            '"Jun"',
            '"Jul"',
            '"Aug"',
            '"Sep"',
            '"Oct"',
            '"Nov"',
            '"Dec"'
        );
        $days = array(
            '"Sun"',
            '"Mon"',
            '"Tue"',
            '"Wed"',
            '"Thu"',
            '"Fri"',
            '"Sat"'
        );

        $hours = array(
            '"6:00 am"',
            '"8:00 am"',
            '"10:00 am"',
            '"12:00 pm"',
            '"2:00 pm"',
            '"4:00 pm"',
            '"6:00 pm"',
            '"8:00 pm"',
            '"10:00 pm"',
            '"12:00 am"',
            '"2:00 am"',
            '"4:00 am"'
        );

        $monthlyOrder = [];
        $label = [];

        switch ($dateRange) {
            case "this_week":
                $weekStartDate = now()->startOfWeek();
                for ($i = 1; $i <= 7; $i++) {
                    $monthlyOrder[$i] = $this->trip->whereNotNull('coupon_amount')
                        ->whereDay('created_at', $weekStartDate->format('d'))->whereMonth('created_at', now()->format('m'))
                        ->sum('coupon_amount');
                    $weekStartDate = $weekStartDate->addDays(1);
                }
                $label = $days;
                $data = $monthlyOrder;
                break;
            case "this_month":
                $start = now()->startOfMonth();
                $end = now()->startOfMonth()->addDays(6);
                $total_day = now()->daysInMonth;
                $remaining_days = now()->daysInMonth - 28;
                $weeks = array(
                    '"Day 1-7"',
                    '"Day 8-14"',
                    '"Day 15-21"',
                    '"Day 22-' . $total_day . '"',
                );
                for ($i = 1; $i <= 4; $i++) {
                    $monthlyOrder[$i] = $this->trip->whereNotNull('coupon_amount')
                        ->whereBetween('created_at', ["{$start->format('Y-m-d')} 00:00:00", "{$end->format('Y-m-d')} 23:59:59"])
                        ->sum('coupon_amount');
                    $start = $start->addDays(7);
                    $end = $i == 3 ? $end->addDays(7 + $remaining_days) : $end->addDays(7);
                }
                $label = $weeks;
                $data = $monthlyOrder;
                break;
            case "this_year":
                for ($i = 1; $i <= 12; $i++) {
                    $monthlyOrder[$i] = $this->trip->whereNotNull('coupon_amount')
                        ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                        ->sum('coupon_amount');
                }
                $label = $months;
                $data = $monthlyOrder;
                break;
            default:
                $start_time = strtotime('6:00 AM');

                for ($i = 0; $i < 12; $i++) {
                    $monthlyOrder[$i] = $this->trip->whereNotNull('coupon_amount')
                    ->whereBetween('created_at', [date('Y-m-d', strtotime('today')) . ' ' . date('H:i:s', $start_time), date('Y-m-d', strtotime('today')) . ' ' . date('H:i:s', strtotime('+2 hours', $start_time))])
                        ->sum('coupon_amount');
                    $start_time = strtotime('+2 hours', $start_time);
                }
                $label = $hours;
                $data = $monthlyOrder;
        }

        return [$label, $data];

    }

    public function getCardValues($dateRange)
    {
        switch ($dateRange) {
            case "this_week":
                $_start_date = Carbon::now()->startOfWeek();
                $_end_date = Carbon::now()->endOfWeek();
                $coupon = $this->coupon->where(function ($query) use ($_start_date, $_end_date) {
                    $query->where(function ($query) use ($_start_date, $_end_date) {
                        $query->whereRaw("end_date >= date('$_start_date')")
                            ->whereRaw("start_date <= date('$_end_date')");
                    });
                })->count();
                $couponInactive = $this->coupon
                    ->whereBetween('end_date', [$_start_date, $_end_date])
                    ->count();
                $trip = $this->trip->whereNotNull('coupon_amount')
                    ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ->get();

                $data = [
                    'total_coupon_amount' => $trip->sum('coupon_amount'),
                    'total_active' => $coupon,
                    'total_inactive' => $couponInactive,
                    'average_coupon_amount' => $trip->avg('coupon_amount'),
                ];
                break;
            case "this_month":
                $_start_date = Carbon::now()->startOfMonth();
                $_end_date = Carbon::now()->endOfMonth();
                $coupon = $this->coupon->where(function ($query) use ($_start_date, $_end_date) {
                    $query->where(function ($query) use ($_start_date, $_end_date) {
                        $query->whereRaw("end_date >= date('$_start_date')")
                            ->whereRaw("start_date <= date('$_end_date')");
                    });
                })->count();
                $couponInactive = $this->coupon
                    ->whereBetween('end_date', [$_start_date, $_end_date])
                    ->count();
                $trip = $this->trip->whereNotNull('coupon_amount')
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->get();

                $data = [
                    'total_coupon_amount' => $trip->sum('coupon_amount'),
                    'total_active' => $coupon,
                    'total_inactive' => $couponInactive,
                    'average_coupon_amount' => $trip->avg('coupon_amount'),
                ];
                break;
            case "this_year":
                $_start_date = Carbon::now()->firstOfYear();
                $_end_date = Carbon::now()->endOfYear();
                $coupon = $this->coupon->where(function ($query) use ($_start_date, $_end_date) {
                    $query->where(function ($query) use ($_start_date, $_end_date) {
                        $query->whereRaw("end_date >= date('$_start_date')")
                            ->whereRaw("start_date <= date('$_end_date')");
                    });
                })->count();
                $couponInactive = $this->coupon
                    ->whereBetween('end_date', [$_start_date, $_end_date])
                    ->count();
                $trip = $this->trip->whereNotNull('coupon_amount')
                    ->whereYear('created_at', Carbon::now()->year)
                    ->get();

                $data = [
                    'total_coupon_amount' => $trip->sum('coupon_amount'),
                    'total_active' => $coupon,
                    'total_inactive' => $couponInactive,
                    'average_coupon_amount' => $trip->avg('coupon_amount'),
                ];
                break;
            default:
                $coupon = $this->coupon->whereDate('start_date', '<=', Carbon::today())
                    ->whereDate('end_date', '>=', Carbon::today())
                    ->count();
                $couponInactive = $this->coupon->whereDate('start_date', '>', Carbon::today())
                    ->whereDate('end_date', '<', Carbon::today())
                    ->count();
                $trip = $this->trip->whereNotNull('coupon_amount')
                    ->whereDate('created_at', Carbon::today())
                    ->get();

                $data = [
                    'total_coupon_amount' => $trip->sum('coupon_amount'),
                    'total_active' => $coupon,
                    'total_inactive' => $couponInactive,
                    'average_coupon_amount' => $trip->avg('coupon_amount'),
                ];
        }
        return $data;

    }

    public function getAppliedCoupon(array $attributes): mixed
    {
        return $this->coupon->query()
            ->with('categories')
            ->where('coupon_code', $attributes['coupon_code'])
            ->where('min_trip_amount', '<=', $attributes['fare'])
            ->where('start_date', '<=', $attributes['date'])
            ->where('end_date', '>=', $attributes['date'])
            ->where('is_active',1)
            ->first();
    }

    public function removeCouponUsage(array $attributes): mixed
    {
        $coupon = $this->coupon->firstWhere('id', $attributes['id']);
        $coupon->decrement('total_used');
        $coupon->total_amount -= $attributes['amount'];
        $coupon->save();

        return $coupon;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function trashed(array $attributes)
    {
        $search = $attributes['search'] ?? null;
        return $this->coupon->query()
            ->when($search, function ($query) use ($search) {
                $keys = explode(' ', $search);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->where('name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->onlyTrashed()
            ->paginate(paginationLimit())
            ->appends(['search' => $search]);

    }

    /**
     * @param string $id
     * @return mixed
     */

    public function restore(string $id)
    {
        return $this->coupon->query()->onlyTrashed()->find($id)->restore();
    }

    public function permanentDelete(string $id): Model
    {
        $model = $this->coupon->query()->onlyTrashed()->find($id);
        $model->forceDelete();
        return $model;
    }

    public function userCouponList(array $attributes)
    {
        return $this->coupon
            ->where(fn($query) => $query->where('user_id', 'all')
                ->orWhere('user_id', $attributes['user_id'])
                ->orWhere('user_level_id', $attributes['level_id']))
            ->where('is_active', $attributes['is_active'])
            ->whereDate('start_date', '<=', $attributes['date'])
            ->whereDate('end_date', '>=', $attributes['date'])
            ->paginate(perPage: $attributes['limit'], page: $attributes['offset']);
    }
}

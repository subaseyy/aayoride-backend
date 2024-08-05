<?php

namespace Modules\UserManagement\Http\Controllers\Api\Driver;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\TransactionManagement\Traits\TransactionTrait;
use Modules\UserManagement\Entities\LoyaltyPointsHistory;
use Modules\UserManagement\Interfaces\DriverInterface;
use Modules\UserManagement\Interfaces\LoyaltyPointsHistoryInterface;
use Modules\UserManagement\Lib\LoyaltyPointHistoryTrait;
use Modules\UserManagement\Transformers\LoyaltyPointsHistoryResource;

class LoyaltyPointController extends Controller
{
    use TransactionTrait;

    public function __construct(
        private DriverInterface $driver,
        private LoyaltyPointsHistoryInterface $history
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|integer',
            'offset' => 'required|integer',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $attributes = [
            'column' => 'user_id',
            'value' => auth('api')->id()
        ];
        $history = $this->history->get(limit: $request->limit,
            offset: $request->offset,
            dynamic_page: true,
            attributes: $attributes);
        $history = LoyaltyPointsHistoryResource::collection($history);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $history, limit: $request->limit, offset: $request->offset));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function convert(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'points' => 'required',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $conversion_rate = businessConfig('loyalty_points', 'driver_settings')?->value;
        $user = auth('api')->user();
        if (($conversion_rate['status'] ?? false) && $user->loyalty_points >= $request->points && $request->points >= ($conversion_rate['points'] ?? 1)) {

            DB::beginTransaction();
            $driver = auth()->user();
            $driver->loyalty_points -= $request->points;
            $driver->save();

            $balance = $request->points / ($conversion_rate['points'] ?? 1);
            $account = $this->driverLoyaltyPointsTransaction($driver, $balance);
            $attributes = [
                'user_id' => $user->id,
                'model_id' => $account->id,
                'model' => 'userAccount',
                'points' => $request->points,
                'type' => 'debit'
            ];
            $this->history->store($attributes);

            DB::commit();

            return response()->json(responseFormatter(constant: DEFAULT_UPDATE_200));
        }

        return response()->json(responseFormatter(constant: INSUFFICIENT_POINTS_403), 403);
    }

}

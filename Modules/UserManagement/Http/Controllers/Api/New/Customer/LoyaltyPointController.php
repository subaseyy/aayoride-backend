<?php

namespace Modules\UserManagement\Http\Controllers\Api\New\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Transformers\LoyaltyPointsHistoryResource;

class LoyaltyPointController extends Controller
{
    protected $customerService;

    public function __construct(CustomerServiceInterface $customerService)
    {
        $this->customerService = $customerService;
    }

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
        $conversion_rate = businessConfig('loyalty_points', 'customer_settings')?->value;
        $user = auth('api')->user();
        if (($conversion_rate['status'] ?? false) && $user->loyalty_points >= $request->points && $request->points >= ($conversion_rate['points'] ?? 1)) {
            DB::beginTransaction();
            $driver = $this->customer->update(attributes: [
                'column' => 'id',
                'decrease' => $request->points,
            ], id: $user->id);
            $balance = $request->points / ($conversion_rate['points'] ?? 1);
            $account = $this->customerLoyaltyPointsTransaction($driver, $balance);
            $attributes = [
                'user_id' => $user->id,
                'model_id' => $account->id,
                'model' => 'user_account',
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

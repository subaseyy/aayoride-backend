<?php

namespace Modules\UserManagement\Http\Controllers\Api\Driver;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\TripManagement\Interfaces\TripRequestInterfaces;
use Modules\UserManagement\Transformers\DriverLeaderBoardResourse;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityController extends Controller
{
    public function __construct(
        private TripRequestInterfaces $trip
    )
    {
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function leaderboard(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric',
            'offset' => 'required|numeric'
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }
        $attributes =  [
            'relations' => 'driver',
            'whereNotNull' => 'driver_id',
            'selectRaw' => 'driver_id, count(*) as total_records , SUM(paid_fare) as income',
            'groupBy' => 'driver_id',
            'orderBy' => 'total_records',
            'direction' => 'desc',
            'start' => now()->copy()->startOfYear(),
            'end' => now()->copy()->endOfYear(),
            'limit' => $request->limit,
            'offset' => $request->offset,

        ];
        $leadDriver = $this->trip->leaderBoard($attributes);
        $leadDriver = DriverLeaderBoardResourse::collection($leadDriver);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $leadDriver, limit: $request->limit, offset: $request->offset));
    }

    /**
     * @return JsonResponse
     */
    public function dailyIncome(): JsonResponse
    {
        $total_income =  [
            'column' => 'driver_id',
            'value' => auth('api')->id(),
            'sum' => 'paid_fare',
            'from' => now()->startOfDay(),
            'to' => now()->endOfDay(),
        ];
        $totalTrip =  [
            'column' => 'driver_id',
            'value' => auth('api')->id(),
            'count' => 'id',
            'from' => now()->startOfDay(),
            'to' => now()->endOfDay(),
        ];
        $total_income = $this->trip->getStat($total_income);
        $totalTrip = $this->trip->getStat($totalTrip);

        return response()->json([
            'total_income' => $total_income,
            'total_trip' => $totalTrip,
            ]
        );
    }


}

<?php

namespace Modules\UserManagement\Http\Controllers\Api\New\Driver;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;
use Modules\UserManagement\Service\Interface\CustomerServiceInterface;
use Modules\UserManagement\Service\Interface\DriverServiceInterface;
use Modules\UserManagement\Transformers\DriverLeaderBoardResourse;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityController extends Controller
{
    protected $driverService;
    protected $tripRequestService;

    public function __construct(DriverServiceInterface $driverService, TripRequestServiceInterface $tripRequestService)
    {
        $this->driverService = $driverService;
        $this->tripRequestService = $tripRequestService;
    }

    public function leaderboard(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'filter' => 'required',
            'limit' => 'required|numeric',
            'offset' => 'required|numeric'
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }
        $request->merge(['user_type' => DRIVER]);
        $request->merge(['data' => $request->filter]);
        $leadDriver = $this->tripRequestService->getLeaderBoard(data: $request->all(),limit: $request->limit,offset: $request->offset);
        $leadDriver = DriverLeaderBoardResourse::collection($leadDriver);
        return response()->json(responseFormatter(constant: DEFAULT_200, content: $leadDriver, limit: $request->limit, offset: $request->offset));
    }

    /**
     * @return JsonResponse
     */
    public function dailyIncome(): JsonResponse
    {
        $data = [
            'user_type'=>DRIVER,
            'driver_id'=>auth('api')->id(),
            'data'=>'today'
        ];
        $driverLead = $this->tripRequestService->getLeaderBoard($data);
        $driverLead = DriverLeaderBoardResourse::make($driverLead);
        return response()->json([
                'total_income' => $driverLead[0]?->income??0,
                'total_trip' => $driverLead[0]?->total_records??0,
            ]
        );
    }
}

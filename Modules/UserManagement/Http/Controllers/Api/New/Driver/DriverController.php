<?php

namespace Modules\UserManagement\Http\Controllers\Api\New\Driver;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;
use Modules\TripManagement\Transformers\TripRequestResource;
use Modules\UserManagement\Service\Interface\DriverDetailServiceInterface;
use Modules\UserManagement\Service\Interface\DriverServiceInterface;
use Modules\UserManagement\Service\Interface\DriverTimeLogServiceInterface;
use Modules\UserManagement\Service\Interface\TimeLogServiceInterface;
use Modules\UserManagement\Transformers\DriverResource;
use Modules\UserManagement\Transformers\DriverTimeLogResource;

class DriverController extends Controller
{
    protected $driverService;
    protected $driverDetailService;
    protected $driverTimeLogService;
    protected $tripRequestService;

    public function __construct(DriverServiceInterface $driverService,DriverDetailServiceInterface $driverDetailService,
                                DriverTimeLogServiceInterface $driverTimeLogService, TripRequestServiceInterface $tripRequestService)
    {
        $this->driverService = $driverService;
        $this->driverDetailService = $driverDetailService;
        $this->driverTimeLogService = $driverTimeLogService;
        $this->tripRequestService = $tripRequestService;
    }

    public function profileInfo(Request $request): JsonResponse
    {
        if (strcmp($request->user()->user_type, DRIVER_USER_TYPES) == 0) {

            $relations = [
                'level', 'vehicle', 'vehicle.brand', 'vehicle.model', 'vehicle.category', 'driverDetails', 'userAccount', 'latestTrack'];
            $withAvgRelations = [
                ['receivedReviews', 'rating']
            ];

            $driver = $this->driverService->findOneBy(criteria: ['id'=>auth()->user()->id], withAvgRelations: $withAvgRelations, relations: $relations);
            $driver = DriverResource::make($driver);

            return response()->json(responseFormatter(DEFAULT_200, $driver), 200);
        }
        return response()->json(responseFormatter(DEFAULT_401), 401);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'unique:users,email,' . $request->user()->id,
            'profile_image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
            'identity_images' => 'sometimes|array',
            'identity_images.*' => 'image|mimes:jpeg,jpg,png,gif|max:10000'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $this->driverService->update(id: $request->user()->id, data: $request->all());

        return response()->json(responseFormatter(DEFAULT_UPDATE_200), 200);
    }

    /**
     * @return JsonResponse
     */
    public function onlineStatus(): JsonResponse
    {
        $driver = auth()->user();
        $details = $this->driverDetailService->findOneBy(criteria: ['user_id'=> $driver->id]);
        $attributes = [
            'column' => 'user_id',
            'is_online' => $details['is_online'] == 1 ? 0 : 1,
            'availability_status' => $details['is_online'] == 1 ? 'unavailable' : 'available',
        ];
        $this->driverService->update(data: $attributes, id: $driver->id);
        // Time log set into driver details
//        $this->details->setTimeLog(
//            driver_id:$driver->id,
//            date:date('Y-m-d'),
//            online:($details->is_online == 1 ? now() : null),
//            offline:($details->is_online == 1 ? null : now()),
//            activeLog:true
//        );

        return response()->json(responseFormatter(DEFAULT_STATUS_UPDATE_200));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function myActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to' => 'required_with:from|date',
            'from' => 'required_with:to|date',
            'limit' => 'required|numeric',
            'offset' => 'required|numeric'
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }

        $user = auth()->user();
        $attributes = [
            'driver_id' => $user->id,
        ];

        $whereBetweenCriteria = [];
        if ($request['to']) {
            $from = Carbon::parse($request['from'])->startOfDay();
            $to = Carbon::parse($request['to'])->endOfDay();
            $whereBetweenCriteria = [
                'created_at' => [$from, $to],
            ];
        }

        $data = $this->driverTimeLogService->getBy(criteria:$attributes, whereBetweenCriteria: $whereBetweenCriteria,limit: $request['limit'], offset: $request['offset']);
        $activity = DriverTimeLogResource::collection($data);
        return response()->json(responseFormatter(DEFAULT_200, $activity, $request['limit'], $request['offset']), 200);

    }

    public function changeLanguage(Request $request): JsonResponse
    {
        if (auth('api')->user()) {
            $this->driverService->changeLanguage(id: auth('api')->user()->id, data: [
                'current_language_key' => $request->header('X-localization') ?? 'en'
            ]);
            return response()->json(responseFormatter(DEFAULT_200), 200);
        }
        return response()->json(responseFormatter(DEFAULT_404), 200);
    }

    public function incomeStatement(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|integer',
            'offset' => 'required|integer',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $criteria = [
            ['driver_id','!=', null],
            'driver_id' => auth()->user()->id,
            'payment_status' => PAID,
        ];
        $incomeStatements = $this->tripRequestService->getBy(criteria: $criteria, limit: $request->limit, offset: $request->offset,orderBy:['updated_at'=>'desc']);
        $incomeStatements = TripRequestResource::collection($incomeStatements);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $incomeStatements, limit: $request->limit, offset: $request->offset));
    }
}

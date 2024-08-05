<?php

namespace Modules\UserManagement\Http\Controllers\Api\Driver;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Entities\DriverTimeLog;
use Modules\UserManagement\Interfaces\DriverDetailsInterface;
use Modules\UserManagement\Interfaces\DriverInterface;
use Modules\UserManagement\Interfaces\DriverTimeLogInterface;
use Modules\UserManagement\Transformers\DriverResource;
use Modules\UserManagement\Transformers\DriverTimeLogResource;

class DriverController extends Controller
{
    public function __construct(
        private DriverInterface $driver,
        private DriverDetailsInterface $details,
        private DriverTimeLogInterface $timeLog
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function profileInfo(Request $request)
//    : JsonResponse
    {
        if(strcmp($request->user()->user_type, DRIVER_USER_TYPES) == 0){

            $attributes = [
                'relations' => [
                    'level', 'vehicle', 'vehicle.brand','vehicle.model', 'vehicle.category', 'driverDetails', 'userAccount', 'latestTrack'],
                'withAvg' => 'receivedReviews',
                'avgColumn' => 'rating',
            ];
            $driver = $this->driver->getBy(column:'id',value:auth()->user()->id, attributes: $attributes);
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
            return response()->json(responseFormatter(constant: DEFAULT_400, errors:  errorProcessor($validator)), 403);
        }

        $this->driver->update(attributes:$request->all(), id:$request->user()->id);

        return response()->json(responseFormatter(DEFAULT_UPDATE_200), 200);
    }

    /**
     * @return JsonResponse
     */
    public function onlineStatus(): JsonResponse
    {
        $driver = auth('api')->user();
        $details = $this->details->getBy('user_id', $driver->id);
        $attributes = [
            'column' => 'user_id',
            'is_online' => $details['is_online'] == 1 ? 0 : 1,
            'availability_status' => $details['is_online'] == 1 ? 'unavailable' : 'available',
        ];
        $this->details->update(attributes: $attributes, id: $driver->id);
        // Time log set into driver details
        $this->details->setTimeLog(
            driver_id:$driver->id,
            date:date('Y-m-d'),
            online:($details->is_online == 1 ? now() : null),
            offline:($details->is_online == 1 ? null : now()),
            activeLog:true
        );

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

        $user = auth('api')->user();
        $attributes = [
            'column' => 'driver_id',
            'value' => $user->id,
        ];

        if ($request['to']) {
            $attributes['from'] = Carbon::parse($request['from'])->startOfDay();
            $attributes['to'] = Carbon::parse($request['to'])->endOfDay();
        }

        $data = $this->timeLog->get(limit: $request['limit'], offset: $request['offset'], dynamic_page: true, attributes: $attributes);
        $activity = DriverTimeLogResource::collection($data);
        return response()->json(responseFormatter(DEFAULT_200, $activity, $request['limit'],$request['offset']), 200);

    }



}

<?php

namespace Modules\TripManagement\Http\Controllers\Api\Driver;

use App\Events\AnotherDriverTripAcceptedEvent;
use App\Events\CustomerTripCancelledEvent;
use App\Events\DriverTripAcceptedEvent;
use App\Events\DriverTripCancelledEvent;
use App\Events\DriverTripCompletedEvent;
use App\Events\DriverTripStartedEvent;
use App\Jobs\SendPushNotificationJob;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Modules\BusinessManagement\Entities\BusinessSetting;
use Modules\ReviewModule\Interfaces\ReviewInterface;
use Modules\TripManagement\Entities\TempTripNotification;
use Modules\TripManagement\Entities\TripRequestCoordinate;
use Modules\TripManagement\Entities\TripRequestTime;
use Modules\TripManagement\Interfaces\FareBiddingInterface;
use Modules\TripManagement\Interfaces\FareBiddingLogInterface;
use Modules\TripManagement\Interfaces\RejectedDriverRequestInterface;
use Modules\TripManagement\Interfaces\TempTripNotificationInterface;
use Modules\TripManagement\Interfaces\TripRequestInterfaces;
use Modules\TripManagement\Interfaces\TripRequestTimeInterface;
use Modules\TripManagement\Transformers\TripRequestResource;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Interfaces\DriverDetailsInterface;
use Modules\UserManagement\Interfaces\UserLastLocationInterface;
use Modules\UserManagement\Lib\LevelHistoryManagerTrait;
use Modules\UserManagement\Lib\LevelUpdateCheckerTrait;
use Ramsey\Uuid\Nonstandard\Uuid;

class TripRequestController extends Controller
{
    use LevelUpdateCheckerTrait;

    public function __construct(
        private TripRequestInterfaces          $trip,
        private FareBiddingInterface           $bidding,
        private FareBiddingLogInterface        $biddingLog,
        private UserLastLocationInterface      $lastLocation,
        private DriverDetailsInterface         $driverDetails,
        private RejectedDriverRequestInterface $rejectedRequest,
        private TempTripNotificationInterface  $tempNotification,
        private ReviewInterface                $review,
        private TripRequestTimeInterface       $time,
    ) {
    }

    public function rideResumeStatus()
    {
        $trip = $this->getIncompleteRide();
        if (!$trip) {
            return response()->json(responseFormatter(constant: DEFAULT_404), 404);
        }
        $trip = TripRequestResource::make($trip);
        return response()->json(responseFormatter(constant: DEFAULT_200, content: $trip));
    }

    /**
     * Summary of bid
     * @param Request $request
     * @return JsonResponse
     */
    public function bid(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        if ($user->driverDetails->availability_status != 'available' || $user->driverDetails->is_online != 1) {

            return response()->json(responseFormatter(constant: DRIVER_UNAVAILABLE_403), 403);
        }

        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
            'bid_fare' => 'numeric',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $trip = $this->trip->getBy('id', $request['trip_request_id'], attributes: ['relations' => 'customer']);

        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404), 403);
        }
        if ($trip->driver_id) {

            return response()->json(responseFormatter(constant: TRIP_REQUEST_DRIVER_403), 403);
        }
        $attributes = [
            'additionalColumn' => 'driver_id',
            'additionalValue' => $user->id
        ];
        $bidding = $this->bidding->getBy(column: 'trip_request_id', value: $request['trip_request_id'], attributes: $attributes);
        if ($bidding) {

            return response()->json(responseFormatter(constant: BIDDING_SUBMITTED_403), 403);
        }
        $this->bidding->store(attributes: [
            'trip_request_id' => $request['trip_request_id'],
            'driver_id' => $user->id,
            'customer_id' => $trip->customer_id,
            'bid_fare' => $request['bid_fare']
        ]);

        $push = getNotification('received_new_bid');
        sendDeviceNotification(
            fcm_token: $trip->customer->fcm_token,
            title: translate($push['title']),
            description: $user?->first_name . $push['description'],
            ride_request_id: $trip->id,
            type: $trip->type,
            action: 'driver_bid_received',
            user_id: $trip->customer->id
        );
        return response()->json(responseFormatter(constant: BIDDING_ACTION_200));
    }

    /**
     * Summary of requestAction
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function requestAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
            'action' => 'required|in:accepted,rejected',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $user = auth('api')->user();
        $cache = Cache::get($request['trip_request_id']);
        $trip = $this->trip->getBy('id', $request['trip_request_id']);
        $user_status = $user->driverDetails->availability_status;

        //start of data for calculation of pickup bonus
        $defaultFare = $trip->zone->defaultFare;
        $pickupBonusAmount = $defaultFare->pickup_bonus_amount;
        $minimumPickupDistance = $defaultFare->minimum_pickup_distance;
        $userCoordinate = [
            "latitude" => $user->lastLocations->latitude,
            "longitude" => $user->lastLocations->longitude,
        ];

        $tripPickupCoordinate = [
            'latitude' => $trip->coordinate->pickup_coordinates->latitude,
            'longitude' => $trip->coordinate->pickup_coordinates->longitude,
        ];
        //end of data for calculation of pickup bonus

        $pickupBonus = $this->getPickupBonus(
            $pickupBonusAmount,
            $minimumPickupDistance,
            $userCoordinate,
            $tripPickupCoordinate
        );

        if ($pickupBonus > 1) {
            $trip->pickup_bonus = $pickupBonus;
            $trip->update();
        }
        

        if ($user_status == 'unavailable' || !$user->driverDetails->is_online) {

            return response()->json(responseFormatter(constant: DRIVER_UNAVAILABLE_403), 403);
        }

        if ($cache == ACCEPTED && $trip->driver_id != $user->id) {
            return response()->json(responseFormatter(TRIP_REQUEST_DRIVER_403), 403);
        }
        if ($cache == ACCEPTED && $trip->driver_id == $user->id) {

            return response()->json(responseFormatter(DEFAULT_UPDATE_200));
        }

        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404), 403);
        }
        if ($trip->driver_id && $trip->driver_id != $user->id) {

            return response()->json(responseFormatter(TRIP_REQUEST_DRIVER_403), 403);
        }


        if ($request['action'] != ACCEPTED) {
            if (get_cache('bid_on_fare') ?? 0) {
                $allBidding = $this->bidding->get(limit: 200, offset: 1, attributes: [
                    'trip_request_id' => $request['trip_request_id'],
                ]);

                if (count($allBidding) > 0) {
                    $this->bidding->destroyData([
                        'column' => 'id',
                        'ids' => $allBidding->pluck('id')
                    ]);
                }
            }
            $data = $this->tempNotification->getBy([
                'trip_request_id' => $request->trip_request_id,
                'user_id' => auth('api')->id()
            ]);
            if ($data) {
                $data->delete();
            }

            $this->rejectedRequest->store([
                'trip_request_id' => $trip->id,
                'user_id' => $user->id
            ]);

            return response()->json(responseFormatter(constant: DEFAULT_UPDATE_200));
        }

        $env = env('APP_MODE');
        $otp = $env != "live" ? '0000' : rand(1000, 9999);

        $driverCurrentStatus = $this->driverDetails->getBy(column: 'user_id', value: $user->id, attributes: [
            'whereInColumn' => 'availability_status',
            'whereInValue' => ['available', 'on_bidding'],
        ]);
        if (!$driverCurrentStatus) {
            return response()->json(responseFormatter(DRIVER_403), 403);
        }
        if ($trip->current_status === "cancelled") {
            return response()->json(responseFormatter(DRIVER_REQUEST_ACCEPT_TIMEOUT_408), 403);
        }
        $bid_on_fare = get_cache('bid_on_fare') ?? 0;
        $attributes = [
            'column' => 'id',
            'driver_id' => $user->id,
            'otp' => $otp,
            'vehicle_id' => $user->vehicle->id,
            'vehicle_category_id' => $user->vehicle->category_id,
            'current_status' => ACCEPTED,
            'trip_status' => ACCEPTED,
        ];
        if (get_cache('bid_on_fare') ?? 0) {
            $bidding = $this->bidding->getBy(
                column: 'trip_request_id',
                value: $request['trip_request_id'],
                attributes: ['additionalColumn' => 'driver_id', 'additionalValue' => $user->id, 'additionalColumn2' => 'is_ignored', 'additionalValue2' => 0],
            );
            if ($bidding) {
                return response()->json(responseFormatter(constant: BIDDING_SUBMITTED_403), 403);
            }
            if ($trip->estimated_fare != $trip->actual_fare) {
                $this->bidding->store(attributes: [
                    'trip_request_id' => $request['trip_request_id'],
                    'driver_id' => $user->id,
                    'customer_id' => $trip->customer_id,
                    'bid_fare' => $trip->actual_fare
                ]);
            }
            $attributes['actual_fare'] = $trip->actual_fare;
        }
        Cache::put($trip->id, ACCEPTED, now()->addHour());
        $driverArrivalTime = getRoutes(
            originCoordinates: [
                $trip->coordinate->pickup_coordinates->latitude,
                $trip->coordinate->pickup_coordinates->longitude
            ],
            destinationCoordinates: [
                $user->lastLocations->latitude,
                $user->lastLocations->longitude
            ],
        );

        $attributes['driver_arrival_time'] = (float)($driverArrivalTime[0]['duration']) / 60;
        //set driver availability_status as on_trip
        $this->driverDetails->update(attributes: ['column' => 'user_id', 'availability_status' => 'on_trip'], id: $user->id);
        //notify other driver about ride started

        $data = $this->tempNotification->get([
            'relations' => 'user',
            'trip_request_id' => $request['trip_request_id'],
            'whereNotInColumn' => 'user_id',
            'whereNotInValue' => [auth('api')->id()]
        ]);

        if (!empty($data)) {
            $push = getNotification('ride_is_started');
            $notification = [
                'title' => translate($push['title']),
                'description' => translate($push['description']),
                'ride_request_id' => $trip->id,
                'type' => $trip->type,
                'action' => 'ride_started'
            ];
            dispatch(new SendPushNotificationJob($notification, $data))->onQueue('high');
            foreach ($data as $tempNotification) {
                try {
                    checkPusherConnection(AnotherDriverTripAcceptedEvent::broadcast($tempNotification->user, $trip));
                } catch (Exception $exception) {
                }
            }
            $this->tempNotification->delete($trip->id);
            TempTripNotification::where('user_id', $user->id)->delete();
        }
        //Trip update
        $trip = $this->trip->update(attributes: $attributes, id: $request['trip_request_id']);
        $trip->tripStatus()->update([
            'accepted' => now()
        ]);
        //deleting exiting rejected driver request for this trip
        $this->rejectedRequest->destroyData([
            'column' => 'trip_request_id',
            'value' => $trip->id,
        ]);

        $push = getNotification('driver_is_on_the_way');
        sendDeviceNotification(
            fcm_token: $trip->customer->fcm_token,
            title: translate($push['title']),
            description: translate($push['description']),
            ride_request_id: $request['trip_request_id'],
            type: $trip->type,
            action: 'driver_assigned',
            user_id: $trip->customer->id
        );
        try {
            checkPusherConnection(DriverTripAcceptedEvent::broadcast($trip));
        } catch (Exception $exception) {
        }
        return response()->json(responseFormatter(constant: DEFAULT_UPDATE_200));
    }


    private function getPickupBonus(
        $pickupBonusAmount,
        $minimumPickupDistance,
        $userCoordinate,
        $tripPickupCoordinate
    ) {
        $finalBonusFare = 0;
        $attributes = [
            'settings_type' => GOOGLE_MAP_API,
            'key_name' => GOOGLE_MAP_API
        ];
        $setting = BusinessSetting::where($attributes)->first();

        if (@$setting->value) {
            $response = Http::get("https://maps.googleapis.com/maps/api/directions/json", [
                "origin" => implode(",", $tripPickupCoordinate),
                "destination" => implode(",", $userCoordinate),
                "key" => $setting->value['map_api_key'],

            ]);
            if ($response->status() === 200) {
                $distanceInMeter = $response->json()['routes'][0]['legs'][0]['distance']['value'];
                $distanceInKM = round($distanceInMeter / 1000);
                if ($distanceInKM > $minimumPickupDistance) {
                    $finalBonusFare = ($distanceInKM - $minimumPickupDistance) * $pickupBonusAmount;
                }
            }
        }

        return ($finalBonusFare);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function rideStatusUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'trip_request_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $user = auth('api')->user();
        $trip = $this->trip->getBy(column: 'id', value: $request['trip_request_id'], attributes: ['relations' => 'customer']);
        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404), 403);
        }
        if ($trip->current_status == 'cancelled') {
            return response()->json(responseFormatter(TRIP_STATUS_CANCELLED_403), 403);
        }
        if ($trip->current_status == 'completed') {
            return response()->json(responseFormatter(TRIP_STATUS_COMPLETED_403), 403);
        }
        if (!$trip) {
            return response()->json(responseFormatter(TRIP_REQUEST_404), 403);
        }
        if ($trip->driver_id != auth('api')->id()) {
            return response()->json(responseFormatter(DEFAULT_400), 403);
        }
        if ($trip->is_paused) {

            return response()->json(responseFormatter(TRIP_REQUEST_PAUSED_404), 403);
        }

        $attributes = [
            'column' => 'id',
            'value' => $request['trip_request_id'],
            'trip_status' => $request['status'],
            'trip_cancellation_reason' => $request['cancel_reason'] ?? null
        ];
        DB::beginTransaction();
        if ($request->status == 'completed' || $request->status == 'cancelled') {
            if ($request->status == 'cancelled') {
                $attributes['fee']['cancelled_by'] = 'driver';
            }
            $attributes['coordinate']['drop_coordinates'] = new Point($trip->driver->lastLocations->latitude, $trip->driver->lastLocations->longitude);

            $this->driverDetails->update(attributes: [
                'column' => 'user_id',
                'availability_status' => 'available'
            ], id: auth('api')->id());
        }

        $data = $this->trip->updateRelationalTable($attributes);
        if ($request->status == 'cancelled') {
            $this->customerLevelUpdateChecker($trip->customer);
            $this->driverLevelUpdateChecker(auth('api')->user());
        } elseif ($request->status == 'completed') {
            $this->customerLevelUpdateChecker($trip->customer);
            $this->driverLevelUpdateChecker(auth('api')->user());
        }
        DB::commit();
        //Get status wise notification message
        if ($trip->type == 'parcel') {
            $action = 'parcel_' . $request->status;
        } else {
            $action = 'ride_' . $request->status;
        }
        $push = getNotification($action);
        sendDeviceNotification(
            fcm_token: $trip->customer->fcm_token,
            title: translate($push['title']),
            description: translate($push['description']),
            ride_request_id: $request['trip_request_id'],
            type: $trip->type,
            action: $action,
            user_id: $trip->customer->id
        );
        if ($request->status == "completed") {
            try {
                checkPusherConnection(DriverTripCompletedEvent::broadcast($trip));
            } catch (Exception $exception) {
            }
        }
        if ($request->status == "cancelled") {
            try {
                checkPusherConnection(DriverTripCancelledEvent::broadcast($trip));
            } catch (Exception $exception) {
            }
        }
        return response()->json(responseFormatter(DEFAULT_UPDATE_200, $data));
    }


    /**
     * Trip otp submit.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function matchOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
            'otp' => 'required|min:4|max:4',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $trip = $this->trip->getBy(column: 'id', value: $request['trip_request_id'], attributes: ['relations' => 'customer', 'coordinate']);


        if (!$trip) {
            return response()->json(responseFormatter(TRIP_REQUEST_404), 403);
        }
        if ($trip->driver_id != auth('api')->id()) {
            return response()->json(responseFormatter(DEFAULT_404), 403);
        }
        if ($trip->otp !== $request['otp']) {

            return response()->json(responseFormatter(OTP_MISMATCH_404), 403);
        }
        DB::beginTransaction();
        $attributes = [
            'column' => 'id',
            'current_status' => ONGOING,
            'trip_status' => now()
        ];

        $trip = $this->trip->update(attributes: $attributes, id: $request['trip_request_id']);
        $trip->tripStatus()->update(['ongoing' => now()]);
        if ($trip->customer->fcm_token) {
            $push = getNotification('trip_started');
            sendDeviceNotification(
                fcm_token: $trip->customer->fcm_token,
                title: translate($push['title']),
                description: translate($push['description']),
                ride_request_id: $request['trip_request_id'],
                type: $trip['type'],
                action: 'otp_matched',
                user_id: $trip->customer->id
            );
        }
        try {
            checkPusherConnection(DriverTripStartedEvent::broadcast($trip));
        } catch (Exception $exception) {
        }
        DB::commit();
        return response()->json(responseFormatter(DEFAULT_STORE_200));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function trackLocation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
            'zoneId' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $request->merge([
            'user_id' => auth('api')->id(),
            'type' => $request->route()->getPrefix() == "api/customer/track-location" ? 'customer' : 'driver',
            'zone_id' => $request->zoneId,
        ]);
        $this->lastLocation->updateOrCreate($request->all());

        return response()->json(responseFormatter(DEFAULT_STORE_200));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function rideList(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required_if:filter,==,custom_date|required_with:end',
            'end' => 'required_if:filter,==,custom_date|required_with:end',
            'limit' => 'required|numeric',
            'offset' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $attributes = [
            'column' => 'driver_id',
            'value' => auth('api')->id(),
            'withAvgRelation' => 'driverReceivedReviews',
            'withAvgColumn' => 'rating',
        ];

        if (!is_null($request->filter) && $request->filter != 'custom_date') {
            $date = getDateRange($request->filter);
        } elseif (!is_null($request->filter)) {
            $date = getDateRange([
                'start' => $request->start,
                'end' => $request->end
            ]);
        }
        if (!empty($date)) {
            $attributes['from'] = $date['start'];
            $attributes['to'] = $date['end'];
        }
        if (!is_null($request->status)) {
            $attributes['column_name'] = 'current_status';
            $attributes['column_value'] = [$request->status];
        }
        $relations = ['customer', 'vehicle.model', 'vehicleCategory', 'time', 'coordinate', 'fee'];
        $data = $this->trip->get(limit: $request['limit'], offset: $request['offset'], dynamic_page: true, attributes: $attributes, relations: $relations);

        $resource = TripRequestResource::setData('distance_wise_fare')::collection($data);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $resource, limit: $request['limit'], offset: $request['offset']));
    }

    /**
     * @param $trip_request_id
     * @param Request $request
     * @return JsonResponse
     */
    public function rideDetails(Request $request, $trip_request_id): JsonResponse
    {
        if (!is_null($request->type) && $request->type == 'overview') {
            $data = $this->rideOverview($trip_request_id, PENDING);
            if (!is_null($data)) {
                $resource = TripRequestResource::make($data);

                return response()->json(responseFormatter(DEFAULT_200, $resource));
            }
        } else {
            $data = $this->rideDetailsFormation($trip_request_id);
            if ($data && auth('api')->id() == $data->driver_id) {
                $resource = TripRequestResource::make($data->append('distance_wise_fare'));

                return response()->json(responseFormatter(DEFAULT_200, $resource));
            }
        }

        return response()->json(responseFormatter(DEFAULT_404), 403);
    }

    /**
     * Show driver pending trip request.
     *
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function pendingRideList(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        if (empty($request->header('zoneId'))) {

            return response()->json(responseFormatter(ZONE_404));
        }
        $user = auth('api')->user();
        
        if ($user->driverDetails->is_online != 1) {

            return response()->json(responseFormatter(constant: DRIVER_UNAVAILABLE_403), 403);
        }
        if ($user?->vehicle?->is_active == 0) {
            return response()->json(responseFormatter(constant: VEHICLE_CATEGORY_404, content: []), 403);
        }

        if ($user->driverDetails->availability_status == 'on_trip') {

            return response()->json(responseFormatter(DEFAULT_200));
        }
        $search_radius = (float)get_cache('search_radius') ?? 5;
        $location = $this->lastLocation->getBy(column: 'user_id', value: $user->id);
        if (!$location) {

            return response()->json(responseFormatter(constant: DEFAULT_200, content: ''));
        }
        if (!$user->vehicle) {

            return response()->json(responseFormatter(constant: DEFAULT_200, content: ''));
        }
        //        $locations = new Point($location->latitude, $location->longitude);
        //        $locations = new Point($location->longitude, $location->latitude);
        $pending_rides = $this->trip->getPendingRides(attributes: [
            'vehicle_category_id' => $user->vehicle->category_id,
            'driver_locations' => $location,
            'distance' => $search_radius * 1000,
            'zone_id' => $request->header('zoneId'),
            'relations' => ['customer', 'ignoredRequests', 'time', 'fee', 'fare_biddings'],
            'withAvgRelation' => 'customerReceivedReviews',
            'withAvgColumn' => 'rating',
            'limit' => $request['limit'],
            'offset' => $request['offset']
        ]);
        $trips = TripRequestResource::collection($pending_rides);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $trips, limit: $request['limit'], offset: $request['offset']));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function lastRideDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:ongoing,last_trip',
            'trip_type' => 'required|in:ride_request,parcel',

        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $trip = $this->trip->getBy(column: 'driver_id', value: auth('api')->id(), attributes: ['latest' => true, 'relations' => 'fee', 'column_name' => 'type', 'column_value' => $request->trip_type ?? 'ride_request']);
        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404, content: $trip));
        }

        $data = [];
        $data[] = TripRequestResource::make($trip->append('distance_wise_fare'));

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $data));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function rideWaiting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $time = $this->time->getBy(column: 'trip_request_id', value: $request->trip_request_id);
        $trip = $this->trip->getBy(column: 'id', value: $request->trip_request_id, attributes: ['relations' => ['customer']]);

        if (!$time) {

            return response()->json(responseFormatter(TRIP_REQUEST_404), 403);
        }
        if ($trip->is_paused == 0) {
            $trip->is_paused = 1;
        } else {
            $trip->is_paused = 0;
            $idle_time = Carbon::parse($time->idle_timestamp)->diffInMinutes(now());
            $time->idle_time += $idle_time;
        }
        $time->idle_timestamp = now();
        $time->save();
        $trip->save();

        $push = getNotification('trip_' . $request->waiting_status);
        sendDeviceNotification(
            fcm_token: $trip->customer->fcm_token,
            title: translate($push['title']),
            description: translate($push['description']),
            ride_request_id: $trip->id,
            type: $trip->type,
            action: 'trip_waited_message',
            user_id: $trip->customer->id
        );

        return response()->json(responseFormatter(DEFAULT_UPDATE_200));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function arrivalTime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $time = TripRequestTime::query()
            ->where('trip_request_id', $request->trip_request_id)
            ->first();

        if (!$time) {

            return response()->json(responseFormatter(TRIP_REQUEST_404), 403);
        }
        $time->driver_arrives_at = now();
        $time->save();

        return response()->json(responseFormatter(constant: DEFAULT_UPDATE_200));
    }

    public function coordinateArrival(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
            'is_reached' => 'required|in:coordinate_1,coordinate_2,destination',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $trip = TripRequestCoordinate::query()->firstWhere('trip_request_id', $request->trip_request_id);

        if ($request->is_reached == 'coordinate_1') {
            $trip->is_reached_1 = true;
        }
        if ($request->is_reached == 'coordinate_2') {
            $trip->is_reached_2 = true;
        }
        if ($request->is_reached == 'destination') {
            $trip->is_reached_destination = true;
        }
        $trip->save();

        return response()->json(responseFormatter(DEFAULT_UPDATE_200));
    }

    public function ignoreTripNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $this->tempNotification->ignoreNotification([
            'trip_request_id' => $request->trip_request_id,
            'user_id' => auth('api')->id()
        ]);

        return response()->json(responseFormatter(DEFAULT_200));
    }


    /**
     * @param $trip_request_id
     * @param $status
     * @return mixed
     */
    private function rideOverview($trip_request_id, $status): mixed
    {
        return $this->trip->getBy(column: 'id', value: $trip_request_id, attributes: [
            'relations' => ['customer', 'vehicleCategory', 'tripStatus', 'time', 'coordinate', 'fee', 'parcel', 'parcelUserInfo'],
            'fare_biddings' => auth('api')->id(),
            'column_name' => 'current_status',
            'column_value' => $status,
            'withAvgRelation' => 'customerReceivedReviews',
            'withAvgColumn' => 'rating'
        ]);
    }

    /**
     * @param $trip_request_id
     * @return mixed
     */
    private function rideDetailsFormation($trip_request_id): mixed
    {
        return $this->trip->getBy(column: 'id', value: $trip_request_id, attributes: [
            'relations' => ['customer', 'vehicleCategory', 'tripStatus', 'time', 'coordinate', 'fee', 'parcel', 'parcelUserInfo'],
            'withAvgRelation' => 'customerReceivedReviews',
            'withAvgColumn' => 'rating'
        ]);
    }

    private function getIncompleteRide()
    {
        $trip = $this->trip->getBy(column: 'driver_id', value: auth()->guard('api')->id(), attributes: [
            'relations' => ['tripStatus', 'customer', 'driver', 'time', 'coordinate', 'time', 'fee'],
            'withAvgRelation' => 'customerReceivedReviews',
            'withAvgColumn' => 'rating'
        ]);

        if (
            !$trip || $trip->fee->cancelled_by == 'driver' ||
            (!$trip->driver_id && $trip->current_status == 'cancelled') ||
            ($trip->driver_id && $trip->payment_status == PAID)
        ) {
            return null;
        }
        return $trip;
    }

    private function getIncompleteRideCustomer($id): mixed
    {
        $trip = $this->trip->getBy(column: 'customer_id', value: $id, attributes: [
            'relations' => ['fee']
        ]);

        if (
            !$trip || $trip->type != 'ride_request' ||
            $trip->fee->cancelled_by == 'driver' ||
            (!$trip->driver_id && $trip->current_status == 'cancelled') ||
            ($trip->driver_id && $trip->payment_status == PAID)
        ) {

            return null;
        }
        return $trip;
    }


    /**
     * @param Request $request
     * @return array|JsonResponse
     */
    public function tripOverView(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filter' => 'required|in:today,this_week,previous_week',
        ]);
        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        if ($request->filter == 'this_week') {
            $start = now()->startOfWeek();
            $end = now()->endOfWeek();
            $day = ['Mon', 'Tues', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        }
        if ($request->filter == 'previous_week') {
            $start = Carbon::now()->subWeek()->startOfWeek();
            $end = Carbon::now()->subWeek()->endOfWeek();
            $day = ['Mon', 'Tues', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        }
        if ($request->filter == 'today') {
            $start = Carbon::today()->startOfDay();
            $end = Carbon::today()->endOfDay();
            $day = [
                '6:00 am',
                '10:00 am',
                '2:00 pm',
                '6:00 pm',
                '10:00 pm',
                '2:00 am',
            ];
        }
        $trips = $this->trip->get(limit: 9999999999, offset: 1, attributes: [
            'from' => $start,
            'to' => $end,
            'column' => 'driver_id',
            'value' => auth('api')->id()
        ]);
        if ($request->filter == 'today') {
            $incomeStat = [];
            $startTime = strtotime('6:00 AM');

            for ($i = 0; $i < 6; $i++) {
                $incomeStat[$day[$i]] = $trips
                    ->whereBetween('created_at', [
                        date('Y-m-d', strtotime('today')) . ' ' . date('H:i:s', $startTime),
                        date('Y-m-d', strtotime('today')) . ' ' . date('H:i:s', strtotime('+4 hours', $startTime))
                    ])
                    ->sum('paid_fare');
                $startTime = strtotime('+4 hours', $startTime);
            }
        } else {
            $period = CarbonPeriod::create($start, $end);
            $trips = $this->trip->get(limit: 9999999999, offset: 1, attributes: [
                'from' => $start,
                'to' => $end,
                'column' => 'driver_id',
                'value' => auth('api')->id()
            ]);
            $incomeStat = [];
            foreach ($period as $key => $p) {
                $incomeStat[$day[$key]] = $trips
                    ->whereBetween('created_at', [$p->copy()->startOfDay(), $p->copy()->endOfDay()])
                    ->sum('paid_fare');
            }
        }





        $attributes = [
            'column' => 'received_by',
            'value' => auth('api')->id(),
            'whereBetween' => [$start, $end]
        ];
        $totalReviews = $this->review->get(limit: 9999999999, offset: 1, attributes: $attributes);
        $totalReviews = $totalReviews->count();

        $totalTrips = $trips->count();
        if ($totalTrips == 0) {
            $fallback = 1;
        } else {
            $fallback = $totalTrips;
        }
        $successTrips = $trips->where('current_status', 'completed')->count();
        $cancelTrips = $trips->where('current_status', 'cancelled')->count();
        $totalEarn = $trips->sum('paid_fare');

        return [
            'success_rate' => ($successTrips / $fallback) * 100,
            'total_trips' => $totalTrips,
            'total_earn' => $totalEarn,
            'total_cancel' => $cancelTrips,
            'total_reviews' => $totalReviews,
            'income_stat' => $incomeStat
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function pendingParcelList(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $attributes = [
            'limit' => $request->limit,
            'offset' => $request->offset,
            'column' => 'driver_id',
            'value' => auth('api')->id(),
            'whereNotNull' => 'driver_id',
        ];

        $trips = $this->trip->pendingParcelList($attributes, 'driver');

        $trips = TripRequestResource::collection($trips);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $trips, limit: $request->limit, offset: $request->offset));
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function unpaidParcelRequest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $trips = $this->trip->unpaidParcelRequest([
            'limit' => $request->limit,
            'offset' => $request->offset,
            'column' => 'driver_id',
            'value' => auth('api')->id(),
        ]);
        $trips = TripRequestResource::collection($trips);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $trips, limit: $request->limit, offset: $request->offset));
    }
}

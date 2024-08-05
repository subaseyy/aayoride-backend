<?php

namespace Modules\TripManagement\Http\Controllers\Api\New\Driver;

use App\Jobs\SendPushNotificationJob;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\FareManagement\Service\Interface\ParcelFareServiceInterface;
use Modules\FareManagement\Service\Interface\ParcelFareWeightServiceInterface;
use Modules\FareManagement\Service\Interface\TripFareServiceInterface;
use Modules\Gateways\Traits\Payment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\PromotionManagement\Service\Interface\CouponSetupServiceInterface;
use Modules\ReviewModule\Service\Interface\ReviewServiceInterface;
use Modules\TransactionManagement\Traits\TransactionTrait;
use Modules\TripManagement\Lib\CommonTrait;
use Modules\TripManagement\Lib\CouponCalculationTrait;
use Modules\TripManagement\Service\Interface\FareBiddingServiceInterface;
use Modules\TripManagement\Service\Interface\RecentAddressServiceInterface;
use Modules\TripManagement\Service\Interface\RejectedDriverRequestServiceInterface;
use Modules\TripManagement\Service\Interface\TempTripNotificationServiceInterface;
use Modules\TripManagement\Service\Interface\TripRequestCoordinateServiceInterface;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;
use Modules\TripManagement\Service\Interface\TripRequestTimeServiceInterface;
use Modules\TripManagement\Transformers\TripRequestResource;
use Modules\UserManagement\Interfaces\UserLastLocationInterface;
use Modules\UserManagement\Lib\LevelHistoryManagerTrait;
use Modules\UserManagement\Service\Interface\DriverDetailServiceInterface;
use Modules\UserManagement\Service\Interface\UserLastLocationServiceInterface;
use Modules\UserManagement\Service\Interface\UserServiceInterface;
use Modules\ZoneManagement\Service\Interface\ZoneServiceInterface;

class TripRequestController extends Controller
{

    use CommonTrait, TransactionTrait, Payment, CouponCalculationTrait, LevelHistoryManagerTrait;
    protected $tripRequestservice;
    protected $tempTripNotificationService;
    protected $fareBiddingService;
    protected $userLastLocation;
    protected $userService;
    protected $driverDetailService;
    protected $fareBiddingServiceService;
    protected $rejectedDriverRequestService;
    protected $couponService;
    protected $zoneService;
    protected $tripFareService;
    protected $parcelFareService;
    protected $parcelFareWeightService;
    protected $recentAddressService;
    protected $reviewService;
    protected $tripRequestTimeService;
    protected $tripRequestCoordinateService;

    public function __construct(
        TripRequestServiceInterface $tripRequestservice,
        TempTripNotificationServiceInterface $tempTripNotificationService,
        FareBiddingServiceInterface $fareBiddingService,
        UserLastLocationServiceInterface $userLastLocation,
        UserServiceInterface $userService,
        DriverDetailServiceInterface $driverDetailService,
        RejectedDriverRequestServiceInterface $rejectedDriverRequestService,
        CouponSetupServiceInterface $couponService,
        ZoneServiceInterface $zoneService,
        TripFareServiceInterface $tripFareService,
        ParcelFareWeightServiceInterface $parcelFareWeightService,
        ParcelFareServiceInterface $parcelFareService,
        RecentAddressServiceInterface $recentAddressService,
        ReviewServiceInterface $reviewService,
        TripRequestTimeServiceInterface $tripRequestTimeService,
        TripRequestCoordinateServiceInterface $tripRequestCoordinateService

    ) {
        $this->tripRequestservice = $tripRequestservice;
        $this->tempTripNotificationService = $tempTripNotificationService;
        $this->fareBiddingService = $fareBiddingService;
        $this->userLastLocation = $userLastLocation;
        $this->userService = $userService;
        $this->driverDetailService = $driverDetailService;
        $this->rejectedDriverRequestService = $rejectedDriverRequestService;
        $this->couponService = $couponService;
        $this->zoneService = $zoneService;
        $this->tripFareService = $tripFareService;
        $this->parcelFareWeightService = $parcelFareWeightService;
        $this->parcelFareService = $parcelFareService;
        $this->recentAddressService = $recentAddressService;
        $this->reviewService = $reviewService;
        $this->tripRequestTimeService = $tripRequestTimeService;
        $this->tripRequestCoordinateService = $tripRequestCoordinateService;
    }



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

        $trip = $this->tripRequestservice->findOne(id: $request['trip_request_id'], relations: ['customer']);

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
        $bidding = $this->fareBiddingService->getBy(criteria: ['trip_request_id' => $request['trip_request_id'], 'driver_id' => $user->id]);
        if ($bidding) {

            return response()->json(responseFormatter(constant: BIDDING_SUBMITTED_403), 403);
        }
        $this->fareBiddingService->create(data: [
            'trip_request_id' => $request['trip_request_id'],
            'driver_id' => $user->id,
            'customer_id' => $trip->customer_id,
            'bid_fare' => $request['bid_fare']
        ]);

        $push = getNotification('received_new_bid');
        sendDeviceNotification(
            fcm_token: $trip->customer->fcm_token,
            title: translate($push['title']),
            description: $user->first_name . $push['description'],
            ride_request_id: $trip->id,
            type: $trip->type,
            action: 'driver_bid_received',
            user_id: $trip->customer->id
        );
        return response()->json(responseFormatter(constant: BIDDING_ACTION_200));
    }


    public function arrivalTime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $time = $this->tripRequestTimeService->findOneBy(criteria: ['trip_request_id' => $request->trip_request_id]);
        if (!$time) {

            return response()->json(responseFormatter(TRIP_REQUEST_404), 403);
        }
        $time->driver_arrives_at = now();
        $time->save();

        return response()->json(responseFormatter(constant: DEFAULT_UPDATE_200));
    }


    public function rideResumeStatus()
    {
        $trip = $this->tripRequestservice->getDriverIncompleteRide();
        if (!$trip) {
            return response()->json(responseFormatter(constant: DEFAULT_404), 404);
        }
        $trip = TripRequestResource::make($trip);
        return response()->json(responseFormatter(constant: DEFAULT_200, content: $trip));
    }

    public function requestAction(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $cache = Cache::get($request['trip_request_id']);
        $trip = $this->tripRequestservice->findOne(id: $request['trip_request_id']);
        $user_status = $user->driverDetails->availability_status;
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
                $allBidding = $this->fareBiddingService->getBy(criteria: ['trip_request_id' => $request['trip_request_id']], limit: 200, offset: 1);

                if (count($allBidding) > 0) {
                    foreach ($allBidding->pluck('id') as $b) {
                    }
                    $this->fareBiddingService->delete(id: $b);
                }
            }
            $data = $this->tempTripNotificationService->findOneBy(criteria: [
                'trip_request_id' => $request->trip_request_id,
                'user_id' => auth()->id()
            ]);
            if ($data) {
                $data->delete();
            }

            $this->rejectedDriverRequestService->create(data: [
                'trip_request_id' => $trip->id,
                'user_id' => $user->id
            ]);

            return response()->json(responseFormatter(constant: DEFAULT_UPDATE_200));
        }


        $env = env('APP_MODE');
        $otp = $env != "live" ? '0000' : rand(1000, 9999);

        $driverCurrentStatus = $this->driverDetailService->getBy(criteria: ['user_id' => $user->id], whereInCriteria: ['availability_status' => ['available', 'on_bidding']]);
        if (!$driverCurrentStatus) {

            return response()->json(responseFormatter(DRIVER_403), 403);
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
            $allBidding = $this->fareBiddingService->getBy(criteria: ['trip_request_id' => $request['trip_request_id']], limit: 200, offset: 1);

            if (count($allBidding) > 0) {
                $actual_fare = $allBidding
                    ->where('driver_id', $request['driver_id'])
                    ->firstWhere('trip_request_id', $request['trip_request_id'])
                    ?->bid_fare ?? 0;
                $attributes['actual_fare'] = $actual_fare;
            }
        }
        if ($trip->current_status === "cancelled") {

            return response()->json(responseFormatter(DRIVER_REQUEST_ACCEPT_TIMEOUT_408), 403);
        }
        $push = $this->tripRequestservice->handleRequestActionPushNotification($trip, $user);
        if (!$bid_on_fare) {

            sendDeviceNotification(
                fcm_token: $trip->customer->fcm_token,
                title: translate($push['title']),
                description: translate($push['description']),
                ride_request_id: $request['trip_request_id'],
                type: $trip->type,
                action: 'driver_assigned',
                user_id: $trip->customer->id
            );
            DB::commit();
        } else {
            $allBidding = $this->fareBiddingService->getBy(limit: 200, offset: 1, criteria: [
                'trip_request_id' => $request['trip_request_id'],
            ]);

            if (count($allBidding) > 0) {

                foreach ($allBidding->pluck('id') as $bId) {

                    $this->fareBiddingService->delete(id: $bId);
                }

                DB::commit();

                sendDeviceNotification(
                    fcm_token: $trip->customer->fcm_token,
                    title: translate($push['title']),
                    description: translate($push['description']),
                    ride_request_id: $request['trip_request_id'],
                    type: $trip->type,
                    action: 'driver_assigned',
                    user_id: $trip->customer->id
                );
            }


            return response()->json(responseFormatter(constant: DEFAULT_UPDATE_200));
        }
    }



    public function matchOtp(Request $request): JsonResponse
    {

        $trip = $this->tripRequestservice->findOne(id: $request['trip_request_id'], relations: ['customer', 'coordinate']);

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
            'current_status' => ONGOING,
            'trip_status' => now()
        ];

        $this->tripRequestservice->update(data: $attributes, id: $request['trip_request_id']);
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

        DB::commit();
        return response()->json(responseFormatter(DEFAULT_STORE_200));
    }

    public function rideStatusUpdate(Request $request): JsonResponse
    {

        $user = auth('api')->user();
        $trip = $this->tripRequestservice->findOne(id: $request['trip_request_id'], relations: ['customer']);
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

        $data = $this->tripRequestservice->handleDriverStatusUpdate($request, $trip);
        return response()->json(responseFormatter(DEFAULT_UPDATE_200, $data));
    }



    public function rideList(Request $request): JsonResponse
    {
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
        if (!is_null($request->status)) {
            $attributes['current_status'] = [$request->status];
        }
        $relations = ['customer', 'vehicle.model', 'vehicleCategory', 'time', 'coordinate', 'fee'];
        $data = $this->tripRequestservice->index(criteria: $attributes, limit: $request['limit'], offset: $request['offset'], relations: $relations);

        $resource = TripRequestResource::setData('distance_wise_fare')::collection($data);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $resource, limit: $request['limit'], offset: $request['offset']));
    }


    public function rideDetails(Request $request, $trip_request_id): JsonResponse
    {
        if (!is_null($request->type) && $request->type == 'overview') {
            $data = $this->tripRequestservice->findOneWithAvg(criteria: [['id' => $trip_request_id], ['current_status', PENDING]], relations: [['fare_biddings' => ['driver_id', auth()->id()]], 'customer', 'time', 'coordinate', 'fee'], withAvgRelation: ['customerReceivedReviews', 'rating']);

            if (!is_null($data)) {
                $resource = TripRequestResource::make($data);

                return response()->json(responseFormatter(DEFAULT_200, $resource));
            }
        } else {
            $data = $this->tripRequestservice->findOneWithAvg(criteria: ['id' => $trip_request_id], relations: ['customer', 'vehicleCategory', 'tripStatus', 'time', 'coordinate', 'fee', 'parcel', 'parcelUserInfo'], withAvgRelation: ['customerReceivedReviews', 'rating']);
            if ($data && auth('api')->id() == $data->driver_id) {
                $resource = TripRequestResource::make($data->append('distance_wise_fare'));

                return response()->json(responseFormatter(DEFAULT_200, $resource));
            }
        }
        return response()->json(responseFormatter(DEFAULT_404), 403);
    }
    public function pendingRideList(Request $request): JsonResponse
    {

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
        $location = $this->userLastLocation->findOneBy(criteria: ['user_id' => $user->id]);

        if (!$location) {

            return response()->json(responseFormatter(constant: DEFAULT_200, content: ''));
        }
        if (!$user->vehicle) {

            return response()->json(responseFormatter(constant: DEFAULT_200, content: ''));
        }
        $locations = new Point($location->latitude, $location->longitude);
        $pending_rides = $this->tripRequestservice->getPendingRides(attributes: [
            'vehicle_category_id' => $user->vehicle->category_id,
            'driver_locations' => $locations,
            'distance' => $search_radius * 1000,
            'zone_id' => $request->header('zoneId'),
            'relations' => ['customer', 'ignoredRequests', 'time', 'fee'],
            'withAvgRelation' => 'customerReceivedReviews',
            'withAvgColumn' => 'rating',
            'limit' => $request['limit'],
            'offset' => $request['offset']
        ]);

        $trips = TripRequestResource::collection($pending_rides);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $trips, limit: $request['limit'], offset: $request['offset']));
    }

    public function lastRideDetails(Request $request)
    {

        $criteria = [['driver_id', auth()->id()], ['type', $request->trip_type ?? 'ride_request']];

        $trip = $this->tripRequestservice->findOneBy(criteria: $criteria, relations: ['fee']);

        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404, content: $trip));
        }

        $data = [];
        $data[] = TripRequestResource::make($trip->append('distance_wise_fare'));

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $data));
    }

    public function trackLocation(Request $request): JsonResponse
    {

        $data = [
            'type' => $request->route()->getPrefix() == "api/customer/track-location" ? 'customer' : 'driver',
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
            'zone_id' => $request['zone_id'],
            'created_at' => now(),
            'updated_at' => now()
        ];


        $this->userLastLocation->update(id: auth('api')->id(), data: $data);

        return response()->json(responseFormatter(DEFAULT_STORE_200));
    }

    public function tripOverView(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filter' => 'required|in:this_week,previous_week',
        ]);
        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $data = $this->tripRequestservice->getTripOverview($request->all());
        $totalReviews = $data['totalReviews'];
        $trips = $data['trips'];
        $incomeStat = $data['incomeStat'];

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


    public function rideWaiting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $time = $this->tripRequestTimeService->findOneBy(criteria: ['trip_request_id' => $request->trip_request_id]);
        $trip = $this->tripRequestservice->findOne(id: $request->trip_request_id, relations: ['customer']);

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

    public function ignoreTripNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $tempTripNotification = $this->tempTripNotificationService->findOneBy(criteria: [
            'trip_request_id' => $request->trip_request_id,
            'user_id' => auth()->id()
        ]);
        if (!$tempTripNotification) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404, content: $tempTripNotification));
        }
        $this->tempTripNotificationService->delete(id: $tempTripNotification);
        return response()->json(responseFormatter(DEFAULT_200));
    }


    public function pendingParcelList(Request $request): JsonResponse
    {

        $attributes = [
            'limit' => $request->limit,
            'offset' => $request->offset,
            'column' => 'driver_id',
            'value' => auth()->id(),
            'whereNotNull' => 'driver_id',
        ];

        $trips = $this->tripRequestservice->pendingParcelList($attributes);
        $trips = TripRequestResource::collection($trips);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $trips, limit: $request->limit, offset: $request->offset));
    }


    public function unpaidParcelRequest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $relation = [
            'parcel' => ['payer', 'sender'], 'customer', 'driver', 'vehicle_category', 'vehicle_category.tripFares', 'vehicle', 'coupon',  'time',
            'coordinate', 'fee', 'trip_status', 'zone', 'vehicle.model', 'fare_biddings', 'parcel', 'parcelUserInfo'
        ];

        $criteria = [
            'type' => 'parcel',
            'customer_id' => auth()->id(),
            'payment_status' => UNPAID
        ];

        $whereNotNullCriteria = ['driver_id'];

        $trips = $this->tripRequestservice->getWithAvg(
            criteria: $criteria,
            relations: $relation,
            whereNotNullCriteria: $whereNotNullCriteria,
            limit: $request->limit,
            offset: $request->offset,
        );
        $trips = TripRequestResource::collection($trips);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $trips, limit: $request->limit, offset: $request->offset));
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

        $trip = $this->tripRequestCoordinateService->findOneBy(criteria: ['trip_request_id' => $request->trip_request_id]);

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
}

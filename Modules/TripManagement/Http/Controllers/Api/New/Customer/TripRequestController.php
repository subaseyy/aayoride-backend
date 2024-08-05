<?php

namespace Modules\TripManagement\Http\Controllers\Api\New\Customer;

use App\Events\RideRequestEvent;
use App\Jobs\SendPushNotificationJob;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\BusinessManagement\Http\Requests\RideListRequest;
use Modules\FareManagement\Service\Interface\ParcelFareServiceInterface;
use Modules\FareManagement\Service\Interface\ParcelFareWeightServiceInterface;
use Modules\FareManagement\Service\Interface\TripFareServiceInterface;
use Modules\Gateways\Traits\Payment;
use Modules\PromotionManagement\Service\Interface\CouponSetupServiceInterface;
use Modules\TransactionManagement\Traits\TransactionTrait;
use Modules\TripManagement\Http\Requests\GetEstimatedFaresOrNotRequest;
use Modules\TripManagement\Http\Requests\RideRequestCreate;
use Modules\TripManagement\Lib\CommonTrait;
use Modules\TripManagement\Lib\CouponCalculationTrait;
use Modules\TripManagement\Service\Interface\FareBiddingServiceInterface;
use Modules\TripManagement\Service\Interface\RecentAddressServiceInterface;
use Modules\TripManagement\Service\Interface\RejectedDriverRequestServiceInterface;
use Modules\TripManagement\Service\Interface\TempTripNotificationServiceInterface;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;
use Modules\TripManagement\Service\Interface\TripRequestTimeServiceInterface;
use Modules\TripManagement\Transformers\FareBiddingResource;
use Modules\TripManagement\Transformers\TripRequestResource;
use Modules\UserManagement\Interfaces\UserLastLocationInterface;
use Modules\UserManagement\Lib\LevelHistoryManagerTrait;
use Modules\UserManagement\Service\Interface\DriverDetailServiceInterface;
use Modules\UserManagement\Service\Interface\UserServiceInterface;
use Modules\UserManagement\Transformers\LastLocationResource;
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
    protected $rejectedDriverRequestService;
    protected $couponService;
    protected $zoneService;
    protected $tripFareService;
    protected $parcelFareService;
    protected $parcelFareWeightService;
    protected $recentAddressService;
    protected $tripRequestTimeService;

    public function __construct(
        TripRequestServiceInterface $tripRequestservice,
        TempTripNotificationServiceInterface $tempTripNotificationService,
        FareBiddingServiceInterface $fareBiddingService,
        UserLastLocationInterface $userLastLocation,
        UserServiceInterface $userService,
        DriverDetailServiceInterface $driverDetailService,
        RejectedDriverRequestServiceInterface $rejectedDriverRequestService,
        CouponSetupServiceInterface $couponService,
        ZoneServiceInterface $zoneService,
        TripFareServiceInterface $tripFareService,
        ParcelFareWeightServiceInterface $parcelFareWeightService,
        ParcelFareServiceInterface $parcelFareService,
        RecentAddressServiceInterface $recentAddressService,
        TripRequestTimeServiceInterface $tripRequestTimeService

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
        $this->tripRequestTimeService = $tripRequestTimeService;
    }



    public function createRideRequest(RideRequestCreate $request): JsonResponse
    {
        $trip = $this->tripRequestservice->getCustomerIncompleteRide();
        if ($trip) {

            return response()->json(responseFormatter(INCOMPLETE_RIDE_403), 403);
        }

        if (empty($request->header('zoneId'))) {

            return response()->json(responseFormatter(ZONE_404), 403);
        }

        $pickupCoordinates = json_decode($request['pickup_coordinates'], true);
        $destinationCoordinates = json_decode($request['destination_coordinates'], true);
        $customer_coordinates = json_decode($request['customer_coordinates'], true);
        $pickup_point = new Point($pickupCoordinates[0], $pickupCoordinates[1]);
        $destination_point = new Point($destinationCoordinates[0], $destinationCoordinates[1]);
        $customer_point = new Point($customer_coordinates[0], $customer_coordinates[1]);

        $request->merge([
            'customer_id' => auth('api')->id(),
            'zone_id' => $request->header('zoneId'),
            'pickup_coordinates' => $pickup_point,
            'destination_coordinates' => $destination_point,
            'estimated_fare' => $request->estimated_fare,
            'actual_fare' => (get_cache('bid_on_fare') ?? 0) ? $request->actual_fare : $request->estimated_fare,
            'customer_request_coordinates' => $customer_point,
        ]);

        $trip = $this->tripRequestservice->makeRideRequest($request, $pickupCoordinates);


        return response()->json(responseFormatter(TRIP_REQUEST_STORE_200, $trip));
    }

    public function getEstimatedFare(GetEstimatedFaresOrNotRequest $request): JsonResponse
    {

        $trip = $this->tripRequestservice->getCustomerIncompleteRide();
        if ($trip) {
            return response()->json(responseFormatter(INCOMPLETE_RIDE_403), 403);
        }

        $zoneId = $request->header('zoneId');
        $zone = $this->zoneService->findOne(id: $zoneId);
        if (!$zone) {
            return response()->json(responseFormatter(ZONE_404), 403);
        }

        $user = auth('api')->user();
        $pickupCoordinates = json_decode($request->pickup_coordinates, true);
        $destinationCoordinates = json_decode($request->destination_coordinates, true);

        $intermediate_coordinates = [];
        if (!is_null($request['intermediate_coordinates'])) {
            $intermediate_coordinates = json_decode($request->intermediate_coordinates, true);
            $maximum_intermediate_point = 2;
            if (count($intermediate_coordinates) > $maximum_intermediate_point) {

                return response()->json(responseFormatter(MAXIMUM_INTERMEDIATE_POINTS_403), 403);
            }
        }

        $pickupCoordinatesPoints = new Point($pickupCoordinates[0], $pickupCoordinates[1]);
        $pickup_location_coverage = $this->zoneService->getByPoints($pickupCoordinatesPoints)->whereId($zoneId)->first();

        $destinationCoordinatesPoints = new Point($destinationCoordinates[0], $destinationCoordinates[1]);
        $destination_location_coverage = $this->zoneService->getByPoints($destinationCoordinatesPoints)->whereId($zoneId)->first();

        if (!$pickup_location_coverage || !$destination_location_coverage) {
            return response()->json(responseFormatter(ZONE_RESOURCE_404), 403);
        }
        if ($request->type == 'ride_request') {
            $tripFare = $this->tripFareService->getBy(criteria: ['zone_id' => $zoneId], relations: ['zone', 'vehicleCategory'], limit: 1000, offset: 1);
            //Get to know in zone's vehicle category car and motorcycle available or not
            $available_categories = $tripFare->map(function ($query) {
                return $query->vehicleCategory->type;
            })->unique()
                ->toArray();

            if (empty($available_categories)) {

                return response()->json(responseFormatter(NO_ACTIVE_CATEGORY_IN_ZONE_404), 403);
            }
        }

        if ($request->type == 'parcel') {
            $parcelWeights = $this->parcelFareWeightService->getAll(limit: 99999, offset: 1);
            $parcel_weight_id = null;

            $parcel_category_id = $request->parcel_category_id;

            foreach ($parcelWeights as $pw) {
                if ($request->parcel_weight >= $pw->min_weight && $request->parcel_weight <= $pw->max_weight) {
                    $parcel_weight_id = $pw['id'];
                }
            }
            if (is_null($parcel_weight_id)) {

                return response()->json(responseFormatter(PARCEL_WEIGHT_400), 403);
            }

            $tripFare = $this->parcelFareService->getBy(criteria: [
                'zone_id' => $zoneId,
                'parcel_weight_id' => $parcel_weight_id,
                'parcel_category_id' => $parcel_category_id,
            ]);
        }

        $getRoutes = getRoutes(
            originCoordinates: $pickupCoordinates,
            destinationCoordinates: $destinationCoordinates,
            intermediateCoordinates: $intermediate_coordinates,
            drivingMode: $request->type == 'ride_request' ? (count($available_categories) == 2 ? ["DRIVE", 'TWO_WHEELER'] : ($available_categories[0] == 'car' ? ['DRIVE'] : ['TWO_WHEELER'])) : ['TWO_WHEELER'],
        );
        if ($getRoutes[1]['status'] !== "OK") {
            return response()->json(responseFormatter(ROUTE_NOT_FOUND_404, $getRoutes[1]['error_detail']), 403);
        }
        $estimated_fare = $this->estimatedFare(
            tripRequest: $request->all(),
            routes: $getRoutes,
            zone_id: $zoneId,
            tripFare: $tripFare,
        );
        //Recent address store
        $this->recentAddressService->create(data: [
            'user_id' => $user->id,
            'zone_id' => $zoneId,
            'pickup_coordinates' => $pickupCoordinatesPoints,
            'destination_coordinates' => $destinationCoordinatesPoints,
            'pickup_address' => $request->pickup_address,
            'destination_address' => $request->destination_address,
        ]);

        return response()->json(responseFormatter(DEFAULT_200, $estimated_fare), 200);
    }

    public function rideList(RideListRequest $request): JsonResponse
    {

        if (!is_null($request->filter) && $request->filter != 'custom_date') {
            $date = getDateRange($request->filter);
        } elseif (!is_null($request->filter)) {
            $date = getDateRange([
                'start' => $request->start,
                'end' => $request->end
            ]);
        }
        $criteria = ['customer_id' => auth('api')->id()];
        $whereBetweenCriteria = [];
        if (!empty($date)) {
            $whereBetweenCriteria = ['created_at', [$date['start'], $date['end']]];
        }
        if (!is_null($request->status)) {
            $criteria['current_status'] = [$request->status];
        }

        $relations = ['driver', 'vehicle.model', 'vehicleCategory', 'time', 'coordinate', 'fee'];
        $data = $this->tripRequestservice->getWithAvg(criteria: $criteria, limit: $request['limit'], offset: $request['offset'], relations: $relations, withAvgRelation: ['driverReceivedReviews', 'rating'], whereBetweenCriteria: $whereBetweenCriteria);
        $resource = TripRequestResource::setData('distance_wise_fare')::collection($data);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $resource, limit: $request['limit'], offset: $request['offset']));
    }

    public function rideDetails($trip_request_id): JsonResponse
    {


        $data = $this->tripRequestservice->findOneWithAvg(criteria: ['id' => $trip_request_id], relations: [
            'driver', 'vehicle.model', 'vehicleCategory', 'tripStatus',
            'coordinate', 'fee', 'time', 'parcel', 'parcelUserInfo'
        ], withAvgRelation: ['customerReceivedReviews', 'rating']);
        if (!$data) {

            return response()->json(responseFormatter(DEFAULT_404), 403);
        }
        $resource = TripRequestResource::make($data->append('distance_wise_fare'));
        return response()->json(responseFormatter(DEFAULT_200, $resource));
    }

    public function biddingList($trip_request_id, Request $request): JsonResponse
    {

        $bidding = $this->fareBiddingService->getWithAvg(
            criteria: ['trip_request_id' => $trip_request_id],
            limit: $request['limit'],
            offset: $request['offset'],
            relations: ['driver_last_location', 'driver', 'trip_request', 'driver.vehicle.model'],
            withAvgRelation: ['customerReceivedReviews', 'rating']
        );
        $bidding = FareBiddingResource::collection($bidding);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $bidding, limit: $request['limit'], offset: $request['offset']));
    }


    public function driversNearMe(Request $request): JsonResponse
    {
        if (is_null($request->header('zoneId'))) {

            return response()->json(responseFormatter(ZONE_404));
        }

        $driverList = $this->tripRequestservice->findNearestDriver(
            latitude: $request->latitude,
            longitude: $request->longitude,
            zoneId: $request->header('zoneId'),
            radius: (float)(get_cache('search_radius') ?? 5)
        );
        $lastLocationDriver = LastLocationResource::collection($driverList);
        return response()->json(responseFormatter(constant: DEFAULT_200, content: $lastLocationDriver));
    }

    public function finalFareCalculation(Request $request): JsonResponse
    {
        $trip = $this->tripRequestservice->findOne(
            id: $request['trip_request_id'],
            relations: ['vehicleCategory.tripFares', 'coupon', 'time', 'coordinate', 'fee', 'tripStatus']
        );

        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404), 403);
        }
        if ($trip->current_status != 'completed' && $trip->current_status != 'cancelled' && $trip->type == 'ride_request') {

            return response()->json(responseFormatter(constant: TRIP_STATUS_NOT_COMPLETED_200));
        }

        if ($trip->paid_fare != 0 || ($trip->paid_fare == 0 && $trip->coupon_amount != null)) {

            $trip = new TripRequestResource($trip->append('distance_wise_fare'));
            return response()->json(responseFormatter(constant: DEFAULT_200, content: $trip));
        }

        $fare = $trip->vehicle_category->tripFares->where('zone_id', $request->header('zoneId'))->first();
        if (!$fare) {

            return response()->json(responseFormatter(ZONE_404), 403);
        }

        //final fare calculation trait
        $calculated_data = $this->calculateFinalFare($trip, $fare);

        $attributes = [
            'paid_fare' => round($calculated_data['final_fare'], 2),
            'actual_fare' => round($calculated_data['actual_fare'], 2),
            'column' => 'id',
            'actual_distance' => $calculated_data['actual_distance'],
        ];
        $this->tripRequestservice->update(id: $request['trip_request_id'], data: $attributes);

        $trip = $this->tripRequestservice->findOne(id: $request['trip_request_id'], relations: ['vehicleCategory.tripFares', 'customer', 'driver', 'coupon', 'time', 'coordinate', 'fee', 'tripStatus']);
        $trip = new TripRequestResource($trip->append('distance_wise_fare'));
        return response()->json(responseFormatter(constant: DEFAULT_200, content: $trip));
    }


    public function requestAction(Request $request): JsonResponse
    {


        $trip = $this->tripRequestservice->findOne(id: $request['trip_request_id'], relations: ['coordinate']);
        $driver = $this->userService->findOne(id: $request['driver_id'], relations: ['vehicle', 'driverDetails', 'lastLocations']);
        if (Cache::get($request['trip_request_id']) == ACCEPTED && $trip->driver_id == $driver->id) {

            return response()->json(responseFormatter(DEFAULT_UPDATE_200));
        }

        $user_status = $driver->driverDetails->availability_status;
        if ($user_status != 'on_bidding' && $user_status != 'available') {

            return response()->json(responseFormatter(constant: DRIVER_403), 403);
        }
        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404), 403);
        }
        if (!$driver->vehicle) {

            return response()->json(responseFormatter(constant: DEFAULT_404), 403);
        }
        if (get_cache('bid_on_fare') ?? 0) {
            $checkBid = $this->fareBiddingService->getBy(criteria: ['trip_request_id' => $request['trip_request_id'], 'driver_id' => $request['driver_id']]);

            if (!$checkBid) {
                return response()->json(responseFormatter(constant: DRIVER_BID_NOT_FOUND_403), 403);
            }
        }

        $env = env('APP_MODE');
        $otp = $env != "live" ? '0000' : rand(1000, 9999);
        $attributes = [
            'column' => 'id',
            'driver_id' => $driver->id,
            'otp' => $otp,
            'vehicle_id' => $driver->vehicle->id,
            'current_status' => ACCEPTED,
            'trip_status' => ACCEPTED,
            'vehicle_category_id' => $driver->vehicle->category_id,
        ];

        if ($request['action'] == ACCEPTED) {
            DB::beginTransaction();
            Cache::put($trip->id, ACCEPTED, now()->addHour());

            //set driver availability_status as on_trip
            $this->driverDetailService->update(id: $driver->id, data: ['column' => 'user_id', 'availability_status' => 'on_trip']);

            //deleting exiting rejected driver request for this trip
            $this->rejectedDriverRequestService->deleteBy(criteria: ['trip_request_id' => $trip->id,]);
            if (get_cache('bid_on_fare') ?? 0) {
                $allBidding = $this->fareBiddingService->getBy(criteria: [
                    'trip_request_id' => $request['trip_request_id']
                ], limit: 200, offset: 1);

                if (count($allBidding) > 0) {
                    $actual_fare = $allBidding
                        ->where('driver_id', $request['driver_id'])
                        ->firstWhere('trip_request_id', $request['trip_request_id'])
                        ->bid_fare;
                    $attributes['actual_fare'] = $actual_fare;
                }
            }


            $data = $this->tripRequestservice->findOneBy(criteria: [
                'trip_request_id' => $request['trip_request_id'],
                'user_id' => [$driver->id]
            ], relations: ['user']);

            $push = getNotification('driver_assigned');
            if (!empty($data)) {
                $notification['title'] = translate($push['title']);
                $notification['description'] = translate($push['description']);
                $notification['ride_request_id'] = $trip->id;
                $notification['type'] = $trip->type;
                $notification['action'] = 'ride_started';

                dispatch(new SendPushNotificationJob($notification, $data))->onQueue('high');
                $this->tripRequestservice->delete(id: $trip->id);
            }
            $driverArrivalTime = getRoutes(
                originCoordinates: [
                    $trip->coordinate->pickup_coordinates->getLat(),
                    $trip->coordinate->pickup_coordinates->getLng()
                ],
                destinationCoordinates: [
                    $driver->lastLocations->latitude,
                    $driver->lastLocations->longitude
                ],
            );
            if ($driverArrivalTime[1]['status'] !== "OK") {
                return response()->json(responseFormatter(ROUTE_NOT_FOUND_404, $driverArrivalTime[1]['error_detail']), 403);
            }
            if ($trip->type == 'ride_request') {
                $attributes['driver_arrival_time'] = (float)($driverArrivalTime[0]['duration']) / 60;
            }

            //Trip update
            $this->tripRequestservice->update(id: $request['trip_request_id'], data: $attributes);
            DB::commit();

            $push = getNotification('bid_accepted');
            sendDeviceNotification(
                fcm_token: $driver->fcm_token,
                title: translate($push['title']),
                description: translate($push['description']),
                ride_request_id: $trip->id,
                type: $trip->type,
                action: 'ride_' . $request->action,
                user_id: $driver->id
            );
        } else {
            if (get_cache('bid_on_fare') ?? 0) {
                $allBidding = $this->fareBiddingService->index(criteria: [
                    'trip_request_id' => $request['trip_request_id'],
                ], limit: 200, offset: 1);

                if (count($allBidding) > 0) {
                    foreach ($allBidding->pluck('id') as $bidId) {
                        $this->tripRequestservice->delete(id: $bidId);
                    }
                }
            }
        }

        return response()->json(responseFormatter(constant: BIDDING_ACTION_200));
    }


    public function rideResumeStatus(): JsonResponse
    {
        $trip = $this->tripRequestservice->getCustomerIncompleteRide();
        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404), 403);
        }
        $trip = TripRequestResource::make($trip);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $trip));
    }

    public function pendingParcelList(Request $request): JsonResponse
    {

        $attributes = [
            'limit' => $request->limit,
            'offset' => $request->offset,
            'column' => 'customer_id',
            'value' => auth()->id(),
            'whereNotNull' => 'customer_id',
        ];

        $trips = $this->tripRequestservice->pendingParcelList($attributes);
        $trips = TripRequestResource::collection($trips);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $trips, limit: $request->limit, offset: $request->offset));
    }

    public function applyCoupon(Request $request): JsonResponse
    {

        $trip = $this->tripRequestservice->findOne(id: $request->trip_request_id, relations: ['driver', 'fee']);
        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404), 403);
        }
        if ($trip->coupon_id) {

            return response()->json(responseFormatter(constant: COUPON_APPLIED_403), 403);
        }
        $user = auth('api')->user();
        $date = date('Y-m-d');

        $criteria = [
            ['coupon_code', $request->coupon_code],
            ['min_trip_amount', '<=', $trip->paid_fare],
            ['start_date', '<=', $date],
            ['end_date', '>=', $date]
        ];
        $coupon = $this->couponService->findOneBy($criteria);
        if (!$coupon) {

            return response()->json(responseFormatter(constant: COUPON_404, content: ['discount' => 0]), 403);
        }
        $response = $this->getCouponDiscount($user, $trip, $coupon);

        if ($response['discount'] != 0) {

            $trip = $this->tripRequestservice->validateDiscount(trip: $trip, response: $response, tripId: $request->trip_request_id, cuponId: $coupon->id);

            return response()->json(responseFormatter(constant: $response['message'], content: $trip));
        }

        return response()->json(responseFormatter(constant: $response['message'], content: $trip), 403);
    }

    public function rideStatusUpdate($trip_request_id, Request $request): JsonResponse
    {

        $trip = $this->tripRequestservice->findOne(id: $trip_request_id, relations: ['driver.lastLocations', 'time', 'coordinate', 'fee']);
        //
        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404), 403);
        }
        if ($trip->current_status == 'cancelled') {
            return response()->json(responseFormatter(TRIP_STATUS_CANCELLED_403), 403);
        }
        if ($trip->current_status == 'completed') {
            return response()->json(responseFormatter(TRIP_STATUS_COMPLETED_403), 403);
        }

        $attributes = [
            'column' => 'id',
            'value' => $trip_request_id,
            'trip_status' => $request['status'],
        ];

        if ($request->status == 'cancelled' && ($trip->current_status == ACCEPTED || $trip->current_status == PENDING)) {
            $this->tripRequestservice->handleCancelledTrip($trip, $attributes, $request['trip_request_id']);
        }
        if ($trip->is_paused) {

            return response()->json(responseFormatter(TRIP_REQUEST_PAUSED_404), 403);
        }

        if ($trip->driver_id && ($request->status == 'completed' || $request->status == 'cancelled') && $trip->current_status == ONGOING) {

            $this->handleCompletedOrCancelledTrip($trip, $request, $attributes);
        }

        $updatedTrip =  $this->tripRequestservice->handleCustomerRideStatusUpdate($trip, $request, $attributes);


        return response()->json(responseFormatter(DEFAULT_UPDATE_200, TripRequestResource::make($updatedTrip)));
    }

    public function cancelCoupon(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $trip = $this->tripRequestservice->findOne(id: $request->trip_request_id, relations: ['driver']);
        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404), 403);
        }
        if (is_null($trip->coupon_id)) {
            return response()->json(responseFormatter(constant: COUPON_404), 403);
        }

        DB::beginTransaction();
        $this->tripRequestservice->removeCouponData($trip);
        DB::commit();

        $push = getNotification('coupon_removed');
        sendDeviceNotification(
            fcm_token: $trip->driver->fcm_token,
            title: translate($push['title']),
            description: translate($push['description']),
            ride_request_id: $trip->id,
            type: $trip->type,
            action: 'coupon_removed',
            user_id: $trip->driver->id
        );

        $trip = new TripRequestResource($trip->append('distance_wise_fare'));
        return response()->json(responseFormatter(constant: DEFAULT_UPDATE_200, content: $trip));
    }

    public function ignoreBidding(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bidding_id' => 'required',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $this->fareBiddingService->update(id: $request->bidding_id, data: ['is_ignored' => 1]);

        return response()->json(responseFormatter(constant: DEFAULT_200));
    }

    public function arrivalTime(Request $request): JsonResponse
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
        $time->customer_arrives_at = now();
        $time->save();

        return response()->json(responseFormatter(constant: DEFAULT_UPDATE_200));
    }

    public function storeScreenshot(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
            'file' => 'required|mimes:jpg,png'
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $this->tripRequestservice->update(id: $request->trip_request_id, data: [
            'map_screenshot' => $request->file,
        ],);

        return response()->json(responseFormatter(DEFAULT_200));
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
}

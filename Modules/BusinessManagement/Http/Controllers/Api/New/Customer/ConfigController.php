<?php

namespace Modules\BusinessManagement\Http\Controllers\Api\New\Customer;

use DateTimeZone;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Modules\BusinessManagement\Http\Requests\UserLocationStore;
use Modules\BusinessManagement\Service\Interface\BusinessSettingServiceInterface;
use Modules\BusinessManagement\Service\Interface\CancellationReasonServiceInterface;
use Modules\BusinessManagement\Service\Interface\SettingServiceInterface;

class ConfigController extends Controller
{
    protected $businessSettingService;
    protected $settingService;
    protected $cancellationReasonService;
    public function __construct(BusinessSettingServiceInterface $businessSettingService,SettingServiceInterface $settingService, CancellationReasonServiceInterface $cancellationReasonService)
    {
        $this->businessSettingService = $businessSettingService;
        $this->settingService = $settingService;
        $this->cancellationReasonService = $cancellationReasonService;
    }

    public function configuration()
    {
        $info = $this->businessSettingService->getAll(limit: 999, offset: 1);

        $loyaltyPoints = $info
            ->where('key_name', 'loyalty_points')
            ->firstWhere('settings_type', 'customer_settings')?->value;

        $configs = [
            'is_demo' => (bool)env('APP_MODE') != 'live'?  true : false,
            'maintenance_mode' => (bool) $info->firstWhere('key_name', 'maintenance_mode')?->value ?? false,
            'required_pin_to_start_trip' => (bool) $info->firstWhere('key_name', 'required_pin_to_start_trip')?->value ?? false,
            'add_intermediate_points' => (bool) $info->firstWhere('key_name', 'add_intermediate_points')?->value ?? false,
            'business_name' => (string) $info->firstWhere('key_name', 'business_name')?->value ?? null,
            'logo' => $info->firstWhere('key_name', 'header_logo')->value ?? null,
            'bid_on_fare' => (bool) $info->firstWhere('key_name', 'bid_on_fare')?->value ?? 0 ,
            'country_code' => (string) $info->firstWhere('key_name', 'country_code')?->value ?? null,
            'business_address' => (string) $info->firstWhere('key_name', 'business_address')?->value ?? null,
            'business_contact_phone' => (string) $info->firstWhere('key_name', 'business_contact_phone')?->value ?? null,
            'business_contact_email' => (string) $info->firstWhere('key_name', 'business_contact_email')?->value ?? null,
            'business_support_phone' => (string) $info->firstWhere('key_name', 'business_support_phone')?->value ?? null,
            'business_support_email' => (string) $info->firstWhere('key_name', 'business_support_email')?->value ?? null,
            'conversion_status' =>  (bool) ($loyaltyPoints['status'] ?? false),
            'conversion_rate' => (double) ($loyaltyPoints['points'] ?? 0),
            'websocket_url' => $info->firstWhere('key_name', 'websocket_url')?->value ?? null,
            'websocket_port' => (string) $info->firstWhere('key_name', 'websocket_port')?->value ?? 6001,
            'websocket_key' => env('PUSHER_APP_KEY'),
            'base_url' => url('/') . 'api/v1/',
            'review_status' => (bool) $info->firstWhere('key_name', 'customer_review')?->value ?? null,
            'image_base_url' => [
                'profile_image_driver' => asset('storage/app/public/driver/profile'),
                'banner' => asset('storage/app/public/promotion/banner'),
                'vehicle_category' => asset('storage/app/public/vehicle/category'),
                'vehicle_model' => asset('storage/app/public/vehicle/model'),
                'vehicle_brand' => asset('storage/app/public/vehicle/brand'),
                'profile_image' => asset('storage/app/public/customer/profile'),
                'identity_image' => asset('storage/app/public/customer/identity'),
                'documents' => asset('storage/app/public/customer/document'),
                'level' => asset('storage/app/public/customer/level'),
                'pages' => asset('storage/app/public/business/pages'),
                'conversation' => asset('storage/app/public/conversation'),
                'parcel' => asset('storage/app/public/parcel/category'),
                'payment_method' => asset('storage/app/public/payment_modules/gateway_image')
            ],
            'currency_decimal_point' => $info->firstWhere('key_name', 'currency_decimal_point')?->value ?? null,
            'trip_request_active_time' => (int) $info->firstWhere('key_name', 'trip_request_active_time')?->value ?? 10,
            'currency_code' => $info->firstWhere('key_name', 'currency_code')?->value ?? null,
            'currency_symbol' => $info->firstWhere('key_name', 'currency_symbol')?->value ?? '$',
            'currency_symbol_position' => $info->firstWhere('key_name', 'currency_symbol_position')?->value ?? null,
            'about_us' => $info->firstWhere('key_name', 'about_us')?->value,
            'privacy_policy' => $info->firstWhere('key_name', 'privacy_policy')?->value,
            'terms_and_conditions' => $info->firstWhere('key_name', 'terms_and_conditions')?->value,
            'legal' => $info->firstWhere('key_name', 'legal')?->value,
            'verification' => (bool) $info->firstWhere('key_name', 'customer_verification')?->value ?? 0,
            'sms_verification' => (bool) $info->firstWhere('key_name', 'sms_verification')?->value ?? 0,
            'email_verification' => (bool) $info->firstWhere('key_name', 'email_verification')?->value ?? 0,
            'facebook_login' => (bool) $info->firstWhere('key_name', 'facebook_login')?->value['status'] ?? 0,
            'google_login' => (bool) $info->firstWhere('key_name', 'google_login')?->value['status'] ?? 0,
            'otp_resend_time' => (int) ($info->firstWhere('key_name', 'otp_resend_time')?->value ?? 60),
            'vat_tax' => (double) get_cache('vat_percent') ?? 1,
            'payment_gateways' => $this->getPaymentMethods(),
        ];

        return response()->json($configs);
    }
    public function getPaymentMethods()
    {
        $methods =  $this->settingService->getBy(limit: 999, offset: 1, criteria: ['settings_type' => PAYMENT_CONFIG]);
        $data = [];
        foreach ($methods as $method) {
            $additionalData = json_decode($method->additional_data,true);
            if ($method?->is_active == 1) {
                $data[] = [
                    'gateway' => $method->key_name,
                    'gateway_title' => $additionalData['gateway_title'],
                    'gateway_image' => $additionalData['gateway_image']
                ];
            }
        }
        return collect($data);
    }
    public function pages($page_name)
    {
        $validated = in_array($page_name, ['about_us', 'privacy_and_policy', 'terms_and_conditions']);

        if (!$validated) {
            return response()->json(responseFormatter(DEFAULT_400), 400);
        }

        $data = businessConfig(key: $page_name, settingsType: PAGES_SETTINGS);
        return response(responseFormatter(DEFAULT_200, [$data]));

    }

    public function getRoutes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $trip = $this->trip->getBy('id', $request->trip_request_id, ['relations' => 'coordinate', 'vehicleCategory']);
        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404, errors: errorProcessor($validator)), 403);
        }

        $pickupCoordinates = [
            $trip->coordinate->pickup_coordinates->latitude,
            $trip->coordinate->pickup_coordinates->longitude,
        ];

        $destinationCoordinates = [
            $trip->coordinate->destination_coordinates->latitude,
            $trip->coordinate->destination_coordinates->longitude,
        ];

        $drivingMode = $trip->vehicleCategory->type == 'motor_bike' ? 'TWO_WHEELER' : 'DRIVE';

        $intermediateCoordinates = $trip->coordinate->intermediate_coordinates ? json_decode($trip->coordinate->intermediate_coordinates, true) : [] ;

        return getRoutes(
            originCoordinates:$pickupCoordinates,
            destinationCoordinates:$destinationCoordinates,
            intermediateCoordinates:$intermediateCoordinates,
        ); //["DRIVE", "TWO_WHEELER"]

        $result = [];
        foreach ($getRoutes as $route) {
            if ($route['drive_mode'] == $drivingMode) {
                $result['is_picked'] =  $trip->current_status == ONGOING;
                return [array_merge($result, $route)];
            }
        }

    }

    public function placeApiAutocomplete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search_text' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(DEFAULT_400, null, null, null, errorProcessor($validator)), 400);
        }

        $mapKey = businessConfig(GOOGLE_MAP_API)?->value['map_api_key_server'] ?? null;
        $response = Http::get($this->googleMapBaseApi . '/place/autocomplete/json?input=' . $request['search_text'] . '&key=' . $mapKey);
        return response()->json(responseFormatter(DEFAULT_200, $response->json()), 200);
    }

    public function distanceApi(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'origin_lat' => 'required',
            'origin_lng' => 'required',
            'destination_lat' => 'required',
            'destination_lng' => 'required',
            'mode' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(DEFAULT_400, null, null, null, errorProcessor($validator)), 400);
        }

        $mapKey = businessConfig(GOOGLE_MAP_API)?->value['map_api_key_server'] ?? null;
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $request['origin_lat'] . ',' . $request['origin_lng'] . '&destinations=' . $request['destination_lat'] . ',' . $request['destination_lng'] .'&travelmode='.$request['mode'] . '&key=' . $mapKey);

        return response()->json(responseFormatter(DEFAULT_200, $response->json()), 200);
    }

    public function placeApiDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'placeid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(DEFAULT_400, null, null, null, errorProcessor($validator)), 400);
        }
        $mapKey = businessConfig(GOOGLE_MAP_API)?->value['map_api_key_server'] ?? null;
        $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json?placeid=' . $request['placeid'] . '&key=' . $mapKey);

        return response()->json(responseFormatter(DEFAULT_200, $response->json()), 200);
    }

    public function geocodeApi(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(DEFAULT_400, null, null, null, errorProcessor($validator)), 400);
        }
        $mapKey = businessConfig(GOOGLE_MAP_API)?->value['map_api_key_server'] ?? null;
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $request->lat . ',' . $request->lng . '&key=' . $mapKey);
        return response()->json(responseFormatter(DEFAULT_200, $response->json()), 200);
    }

    public function userLastLocation(UserLocationStore $request)
    {

        if (empty($request->header('zoneId'))) {

            return response()->json(responseFormatter(ZONE_404), 200);
        }

        $zone_id = $request->header('zoneId');
        $user = auth('api')->user();
        $request->merge([
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'zone_id' => $zone_id,
        ]);
        return $this->location->updateOrCreate(attributes:$request->all());
    }

    public function getZone(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(DEFAULT_400, null, null, null, errorProcessor($validator)), 400);
        }

        $point = new Point($request->lat, $request->lng);
        $zone = $this->zone->getByPoints($point)->where('is_active', 1)->latest()->first();

        if ($zone) {
            return response()->json(responseFormatter(DEFAULT_200, $zone), 200);
        }

        return response()->json(responseFormatter(ZONE_RESOURCE_404), 403);
    }

    public function cancellationReasonList()
    {

        $ongoingRide = $this->cancellationReasonService->getBy(criteria: ['cancellation_type'=>'ongoing_ride','user_type'=>'customer','is_active'=>1])->pluck('title')->toArray();
        if (count($ongoingRide)<=0){
            $ongoingRide = collect(CUSTOMER_ONGOING_RIDE_CANCELLATION_REASON);
        }
        $acceptedRide = $this->cancellationReasonService->getBy(criteria: ['cancellation_type'=>'accepted_ride','user_type'=>'customer','is_active'=>1])->pluck('title')->toArray();
        if (count($acceptedRide)<=0){
            $acceptedRide = collect(CUSTOMER_ACCEPT_RIDE_CANCELLATION_REASON);
        }
        $data = [
            'ongoing_ride' => $ongoingRide,
            'accepted_ride' => $acceptedRide,
        ];
        return response(responseFormatter(DEFAULT_200, [$data]));
    }

}

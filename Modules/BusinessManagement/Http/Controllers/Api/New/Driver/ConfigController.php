<?php

namespace Modules\BusinessManagement\Http\Controllers\Api\New\Driver;

use DateTimeZone;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\BusinessManagement\Service\Interface\BusinessSettingServiceInterface;
use Modules\BusinessManagement\Service\Interface\CancellationReasonServiceInterface;

class ConfigController extends Controller
{
    protected $businessSettingService;
    protected $cancellationReasonService;
    public function __construct(BusinessSettingServiceInterface $businessSettingService, CancellationReasonServiceInterface $cancellationReasonService)
    {
        $this->businessSettingService = $businessSettingService;
        $this->cancellationReasonService = $cancellationReasonService;
    }

    public function configuration()
    {
        $info = $this->businessSettingService->getAll(limit: 999, offset: 1);
        $loyaltyPoints = $info
            ->where('key_name', 'loyalty_points')
            ->firstWhere('settings_type', 'driver_settings')?->value;

        $configs = [
            'is_demo' => (bool)env('APP_MODE') != 'live'?  true : false,
            'maintenance_mode' => (bool) $info->firstWhere('key_name', 'maintenance_mode')?->value ?? false,
            'required_pin_to_start_trip' => (bool) $info->firstWhere('key_name', 'required_pin_to_start_trip')?->value ?? false,
            'add_intermediate_points' => (bool) $info->firstWhere('key_name', 'add_intermediate_points')?->value ?? false,
            'business_name' => $info->firstWhere('key_name', 'business_name')?->value ?? null,
            'logo' => $info->firstWhere('key_name', 'header_logo')?->value ?? null,
            'bid_on_fare' => (bool) $info->firstWhere('key_name', 'bid_on_fare')?->value ?? 0,
            'driver_completion_radius' => $info->firstWhere('key_name', 'driver_completion_radius')?->value ?? 10,
            'country_code' => $info->firstWhere('key_name', 'country_code')?->value ?? null,
            'business_address' => $info->firstWhere('key_name', 'business_address')->value ?? null,
            'business_contact_phone' => $info->firstWhere('key_name', 'business_contact_phone')?->value ?? null,
            'business_contact_email' => $info->firstWhere('key_name', 'business_contact_email')?->value ?? null,
            'business_support_phone' => $info->firstWhere('key_name', 'business_support_phone')?->value ?? null,
            'business_support_email' => $info->firstWhere('key_name', 'business_support_email')?->value ?? null,
            'conversion_status' =>  (bool) ($loyaltyPoints['status'] ?? false),
            'conversion_rate' => (float) ($loyaltyPoints['points'] ?? 0),
            'base_url' => url('/') . 'api/v1/',
            'websocket_url' => $info->firstWhere('key_name', 'websocket_url')?->value ?? null,
            'websocket_port' => (string) $info->firstWhere('key_name', 'websocket_port')?->value ?? 6001,
            'websocket_key' => env('PUSHER_APP_KEY'),
            'review_status' => (bool) $info->firstWhere('key_name', 'driver_review')?->value ?? null,
            'image_base_url' => [
                'profile_image_customer' => asset('storage/app/public/customer/profile'),
                'banner' => asset('storage/app/public/promotion/banner'),
                'vehicle_category' => asset('storage/app/public/vehicle/category'),
                'vehicle_model' => asset('storage/app/public/vehicle/model'),
                'vehicle_brand' => asset('storage/app/public/vehicle/brand'),
                'profile_image' => asset('storage/app/public/driver/profile'),
                'identity_image' => asset('storage/app/public/driver/identity'),
                'documents' => asset('storage/app/public/driver/document'),
                'pages' => asset('storage/app/public/business/pages'),
                'conversation' => asset('storage/app/public/conversation'),
                'parcel' => asset('storage/app/public/parcel/category'),
            ],
            'otp_resend_time' => (int) $info->firstWhere('key_name', 'otp_resend_time')?->value ?? 60,
            'currency_decimal_point' => $info->firstWhere('key_name', 'currency_decimal_point')?->value ?? null,
            'currency_code' => $info->firstWhere('key_name', 'currency_code')?->value ?? null,
            'currency_symbol' => $info->firstWhere('key_name', 'currency_symbol')->value ?? '$',
            'currency_symbol_position' => $info->firstWhere('key_name', 'currency_symbol_position')?->value ?? null,
            'about_us' => $info->firstWhere('key_name', 'about_us')?->value ?? null,
            'privacy_policy' => $info->firstWhere('key_name', 'privacy_policy')?->value ?? null,
            'terms_and_conditions' => $info->firstWhere('key_name', 'terms_and_conditions')?->value ?? null,
            'legal' => $info->firstWhere('key_name', 'legal')?->value,
            'verification' => (bool) $info->firstWhere('key_name', 'driver_verification')?->value ?? 0,
            'sms_verification' => (bool) $info->firstWhere('key_name', 'sms_verification')?->value ?? 0,
            'email_verification' => (bool) $info->firstWhere('key_name', 'email_verification')?->value ?? 0,
            'facebook_login' => (bool) $info->firstWhere('key_name', 'facebook_login')?->value['status'] ?? 0,
            'google_login' => (bool) $info->firstWhere('key_name', 'google_login')?->value['status'] ?? 0,
            'time_zones' => DateTimeZone::listIdentifiers(),
            'self_registration' => (bool) $info->firstWhere('key_name', 'driver_self_registration')?->value ?? 0,

        ];

        return response(responseFormatter(DEFAULT_200, [$configs]));
    }

    public function cancellationReasonList()
    {
        $ongoingRide = $this->cancellationReasonService->getBy(criteria: ['cancellation_type'=>'ongoing_ride','user_type'=>'driver','is_active'=>1])->pluck('title')->toArray();
        if (count($ongoingRide)<=0){
            $ongoingRide = collect(DRIVER_ONGOING_RIDE_CANCELLATION_REASON);
        }
        $acceptedRide = $this->cancellationReasonService->getBy(criteria: ['cancellation_type'=>'accepted_ride','user_type'=>'driver','is_active'=>1])->pluck('title')->toArray();
        if (count($acceptedRide)<=0){
            $acceptedRide = collect(DRIVER_ACCEPT_RIDE_CANCELLATION_REASON);
        }
        $data = [
            'ongoing_ride' => $ongoingRide,
            'accepted_ride' => $acceptedRide,
        ];
        return response(responseFormatter(DEFAULT_200, [$data]));
    }
}

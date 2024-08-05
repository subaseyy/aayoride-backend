<?php

namespace App\Http\Controllers;

use App\Traits\ActivationClass;
use App\Traits\UnloadedHelpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Mockery\Exception;
use Modules\BusinessManagement\Entities\FirebasePushNotification;
use Modules\BusinessManagement\Service\Interface\BusinessSettingServiceInterface;
use Modules\BusinessManagement\Service\Interface\FirebasePushNotificationServiceInterface;
use Modules\BusinessManagement\Service\Interface\NotificationSettingServiceInterface;
use Modules\PromotionManagement\Entities\CouponSetup;
use Modules\PromotionManagement\Service\Interface\CouponSetupServiceInterface;
use Modules\UserManagement\Entities\User;
use Illuminate\Support\Facades\Schema;
use Modules\UserManagement\Entities\WithdrawRequest;

class UpdateController extends Controller
{
    use UnloadedHelpers;
    use ActivationClass;

    protected $businessSetting;
    protected $notificationSettingService;
    protected $firebasePushNotificationService;
    protected $couponSetupService;

    public function __construct(BusinessSettingServiceInterface          $businessSetting, NotificationSettingServiceInterface $notificationSettingService,
                                FirebasePushNotificationServiceInterface $firebasePushNotificationService, CouponSetupServiceInterface $couponSetupService)
    {
        $this->businessSetting = $businessSetting;
        $this->notificationSettingService = $notificationSettingService;
        $this->firebasePushNotificationService = $firebasePushNotificationService;
        $this->couponSetupService = $couponSetupService;
    }

    public function update_software_index()
    {
        $modules = ['AdminModule', 'AuthManagement', 'BusinessManagement', 'ChattingManagement', 'FareManagement',
            'Gateways', 'ParcelManagement', 'PromotionManagement', 'ReviewModule', 'TransactionManagement', 'TripManagement',
            'UserManagement', 'VehicleManagement', 'ZoneManagement',
        ];
        foreach ($modules as $module) {
            Artisan::call('module:enable', ['module' => $module]);
        }
        return view('update.update-software');
    }

    public function update_software(Request $request)
    {
        $this->setEnvironmentValue('SOFTWARE_ID', 'MTAwMDAwMDA=');
        $this->setEnvironmentValue('BUYER_USERNAME', $request['username']);
        $this->setEnvironmentValue('PURCHASE_CODE', $request['purchase_key']);
        $this->setEnvironmentValue('SOFTWARE_VERSION', '1.6');
        $this->setEnvironmentValue('APP_ENV', 'local');
        $this->setEnvironmentValue('APP_MODE', 'live');
        $this->setEnvironmentValue('APP_URL', url('/'));
        $this->setEnvironmentValue('PUSHER_APP_ID', 'drivemond');
        $this->setEnvironmentValue('PUSHER_APP_KEY', 'drivemond');
        $this->setEnvironmentValue('PUSHER_APP_SECRET', 'drivemond');
        $this->setEnvironmentValue('PUSHER_HOST', getMainDomain(url('/')));
        $this->setEnvironmentValue('PUSHER_PORT', 6001);
        $this->setEnvironmentValue('PUSHER_APP_CLUSTER', 'mt1');
        $this->setEnvironmentValue('PUSHER_SCHEME', 'http');
        $this->setEnvironmentValue('REVERB_APP_ID', 'drivemond');
        $this->setEnvironmentValue('REVERB_APP_KEY', 'drivemond');
        $this->setEnvironmentValue('REVERB_APP_SECRET', 'drivemond');
        $this->setEnvironmentValue('REVERB_HOST', getMainDomain(url('/')));
        $this->setEnvironmentValue('REVERB_PORT', 6001);
        $this->setEnvironmentValue('REVERB_SCHEME', 'http');
        $this->setEnvironmentValue('REVERB_SSL_CERT_PATH', "");
        $this->setEnvironmentValue('REVERB_SSL_KEY_PATH', "");

        $data = $this->actch();
        try {
            if (!$data->getData()->active) {
                $remove = array("http://", "https://", "www.");
                $url = str_replace($remove, "", url('/'));

                $activation_url = base64_decode('aHR0cHM6Ly9hY3RpdmF0aW9uLmRyaXZlbW9uZC5hcHAv');
                $activation_url .= '?username=' . $request['username'];
                $activation_url .= '&purchase_code=' . $request['purchase_key'];
                $activation_url .= '&domain=' . $url . '&';

                return redirect($activation_url);
            }
        } catch (Exception $exception) {
            Toastr::error('verification failed! try again');
            return back();
        }


        Artisan::call('migrate', ['--force' => true]);

        $previousRouteServiceProvider = base_path('app/Providers/RouteServiceProvider.php');
        $newRouteServiceProvider = base_path('app/Providers/RouteServiceProvider.txt');
        copy($newRouteServiceProvider, $previousRouteServiceProvider);

        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:cache');
        Artisan::call('config:clear');
        Artisan::call('optimize:clear');
        $withdrawRequests = WithdrawRequest::get();
        foreach ($withdrawRequests as $withdrawRequest) {
            if ($withdrawRequest->is_approved == null) {
                $withdrawRequest->status = PENDING;
            } elseif ($withdrawRequest->is_approved == 1) {
                $withdrawRequest->status = SETTLED;
            } else {
                $withdrawRequest->status = DENIED;
            }
            $withdrawRequest->save();
        }
        $users = User::withTrashed()->get();
        foreach ($users as $user) {
            if (is_null($user->full_name)) {
                $user->full_name = $user->first_name . ' ' . $user->last_name;
                $user->save();
            }
        }
        if (Schema::hasColumns('coupon_setups', ['user_id', 'user_level_id', 'rules'])) {
            $couponSetups = CouponSetup::withTrashed()->get();
            if (count((array)$couponSetups) > 0) {
                foreach ($couponSetups as $couponSetup) {
                    $couponSetup->zone_coupon_type = ALL;
                    $couponSetup->save();
                    if ($couponSetup->user_id == ALL) {
                        $couponSetup->customer_coupon_type = ALL;
                        $couponSetup->save();
                    } else {
                        $couponSetup->customer_coupon_type = CUSTOM;
                        $couponSetup->save();
                        $couponSetup?->customers()->attach($couponSetup->user_id);
                    }
                    if ($couponSetup->user_level_id == ALL || $couponSetup->user_level_id == null) {
                        $couponSetup->customer_level_coupon_type = ALL;
                        $couponSetup->save();
                    } else {
                        $couponSetup->customer_level_coupon_type = CUSTOM;
                        $couponSetup->save();
                        $couponSetup?->customerLevels()->attach($couponSetup->user_level_id);
                    }
                    if ($couponSetup->rules == "default") {
                        $couponSetup->category_coupon_type = [ALL];
                        $couponSetup->save();
                    } else {
                        $couponSetup->category_coupon_type = [CUSTOM];
                        $couponSetup->save();
                    }
                }

            }
            Schema::table('coupon_setups', function (Blueprint $table) {
                $table->dropColumn(['user_id', 'user_level_id', 'rules']); // Replace 'column_name' with the actual column name
            });
        }

        $notificationSettings = $this->notificationSettingService->getAll();
        foreach ($notificationSettings as $setting) {
            if (in_array($setting->name, ['trip', 'rating_and_review'])) {
                $this->notificationSettingService->delete($setting->id);
            }
        }
        if ($this->notificationSettingService->findOneBy(criteria: ['name' => 'legal']) == false) {
            $this->notificationSettingService->create(data: [
                'name' => 'legal',
                'push' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'identity_image_approved']) == false) {
            $this->firebasePushNotificationService->create(data: [
                'name' => 'identity_image_approved',
                'value' => 'Your identity image has been successfully reviewed and approved.',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'identity_image_rejected']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'identity_image_rejected',
                'value' => 'Your identity image has been rejected during our review process.',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'review_from_customer']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'review_from_customer',
                'value' => 'New review from a customer! See what they had to say about your service.',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'review_from_driver']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'review_from_driver',
                'value' => 'New review from a driver! See what he had to say about your trip.',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'bid_request_from_driver']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'bid_request_from_driver',
                'value' => 'Driver sent a bid request',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'bid_request_cancel_by_customer']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'bid_request_cancel_by_customer',
                'value' => 'Customer has canceled your bid request',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'fund_added_by_admin']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'fund_added_by_admin',
                'value' => 'Admin has added {walletAmount} to your wallet',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'level_up']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'level_up',
                'value' => 'You have completed your challenges and reached level {levelName}',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'vehicle_is_active']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'vehicle_is_active',
                'value' => 'Your vehicle status has been activated by admin',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'driver_cancel_ride_request']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'driver_cancel_ride_request',
                'value' => 'Driver has canceled your ride',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'driver_cancel_ride_request']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'driver_cancel_ride_request',
                'value' => 'Driver has canceled your ride',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'tips_from_customer']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'tips_from_customer',
                'value' => 'Customer has given the tips {tipsAmount} with payment',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'new_message']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'new_message',
                'value' => 'You got a new message from {userName}',
                'status' => 1
            ]);
        }else{
            $this->firebasePushNotificationService->updatedBy(criteria: ['name' => 'new_message'],data: [
                'value' => 'You got a new message from {userName}',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'payment_successful']) == true) {
            $this->firebasePushNotificationService->updatedBy(criteria: ['name' => 'payment_successful'],
                data:[
                'value' => '{paidAmount} payment successful on this trip by {methodName}.',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'withdraw_request_rejected']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'withdraw_request_rejected',
                'value' => 'Unfortunately, your withdrawal request has been rejected. {withdrawNote}',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'withdraw_request_approved']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'withdraw_request_approved',
                'value' => 'We are pleased to inform you that your withdrawal request has been approved. The funds will be transferred to your account shortly.',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'withdraw_request_settled']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'withdraw_request_settled',
                'value' => 'Your withdrawal request has been successfully settled. The funds have been transferred to your account.',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'withdraw_request_reversed']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'withdraw_request_reversed',
                'value' => 'Your withdrawal request has been successfully settled. The funds have been transferred to your account.',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'customer_bid_rejected']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'customer_bid_rejected',
                'value' => 'We regret to inform you that your bid request for trip ID {tripId} has been rejected by the customer',
                'status' => 1
            ]);
        }
        if ($this->firebasePushNotificationService->findOneBy(criteria: ['name' => 'legal_updated']) == false) {
            $this->firebasePushNotificationService->create(data: ['name' => 'legal_updated',
                'value' => 'We have updated our legal',
                'status' => 1
            ]);
        }
        return redirect(env('APP_URL'));
    }
}

<?php

use App\WebSockets\Handler\UserLocationSocketHandler;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Support\Facades\Route;
use Modules\AuthManagement\Http\Controllers\Api\AuthController;
use Modules\BusinessManagement\Http\Controllers\Api\Customer\ConfigController;
use Modules\BusinessManagement\Http\Controllers\Api\Driver\ConfigController as DriverConfigController;
use Modules\UserManagement\Http\Controllers\Api\AppNotificationController;
use Modules\UserManagement\Http\Controllers\Api\Customer\AddressController;
use Modules\UserManagement\Http\Controllers\Api\Customer\CustomerController;
use Modules\UserManagement\Http\Controllers\Api\Customer\LoyaltyPointController;
use Modules\UserManagement\Http\Controllers\Api\Driver\LoyaltyPointController as DriverPointsController;
use Modules\UserManagement\Http\Controllers\Api\Driver\DriverController;
use Modules\UserManagement\Http\Controllers\Api\Driver\TimeTrackController;
use Modules\UserManagement\Http\Controllers\Api\Driver\WithdrawController;
use Modules\UserManagement\Http\Controllers\Api\New\Driver\WithdrawMethodInfoController;
use Modules\UserManagement\Http\Controllers\Api\UserController;


Route::group(['prefix' => 'customer'], function () {

    Route::group(['prefix' => 'config'], function () {
        Route::get('get-zone-id', [ConfigController::class, 'getZone']);
        Route::get('place-api-autocomplete', [ConfigController::class, 'placeApiAutocomplete']);
        Route::get('distance-api', [ConfigController::class, 'distanceApi']);
        Route::get('place-api-details', [ConfigController::class, 'placeApiDetails']);
        Route::get('geocode-api', [ConfigController::class, 'geocodeApi']);
        Route::post('get-routes', [ConfigController::class, 'getRoutes']);
    });

    Route::group(['middleware' => ['auth:api', 'maintenance_mode']], function () {
        Route::group(['prefix' => 'loyalty-points'], function () {
            Route::get('list', [LoyaltyPointController::class, 'index']);
            Route::post('convert', [LoyaltyPointController::class, 'convert']);
        });
        Route::group(['prefix' => 'level'], function () {
            Route::get('/', [\Modules\UserManagement\Http\Controllers\Api\New\Customer\CustomerLevelController::class, 'getCustomerLevelWithTrip']);
        });
        Route::get('info', [CustomerController::class, 'profileInfo']);
        Route::group(['prefix' => 'update'], function () {
            Route::put('fcm-token', [AuthController::class, 'updateFcmToken']); //for customer and driver use AuthController
            Route::put('profile', [CustomerController::class, 'updateProfile']);
        });
        Route::get('notification-list', [AppNotificationController::class, 'index']);
        Route::controller(\Modules\UserManagement\Http\Controllers\Api\New\Customer\CustomerController::class)->group(function (){
            Route::post('applied-coupon',  'applyCoupon');
            Route::post('change-language',  'changeLanguage');
        });

        Route::group(['prefix' => 'address'], function () {
            Route::get('all-address', [AddressController::class, 'getAddresses']);
            Route::post('add', [AddressController::class, 'store']);
            Route::get('edit/{id}', [AddressController::class, 'edit']);
            Route::put('update', [AddressController::class, 'update']);
            Route::delete('delete', [AddressController::class, 'destroy']);

        });
    });

});

Route::group(['prefix' => 'driver'], function () {
    Route::group(['prefix' => 'config'], function () {
        // These config will found in Customer Config
        Route::get('get-zone-id', [ConfigController::class, 'getZone']);
        Route::get('place-api-autocomplete', [ConfigController::class, 'placeApiAutocomplete']);
        Route::get('distance-api', [ConfigController::class, 'distanceApi']);
        Route::get('place-api-details', [ConfigController::class, 'placeApiDetails']);
        Route::get('geocode-api', [ConfigController::class, 'geocodeApi']);
    });

    Route::group(['middleware' => ['auth:api', 'maintenance_mode']], function () {
        Route::get('income-statement', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\DriverController::class, 'incomeStatement']);
        Route::post('get-routes', [DriverConfigController::class, 'getRoutes']);

        Route::get('time-tracking', [TimeTrackController::class, 'store']);
        Route::post('update-online-status', [TimeTrackController::class, 'onlineStatus']);

        Route::get('info', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\DriverController::class, 'profileInfo']);
        Route::controller(\Modules\UserManagement\Http\Controllers\Api\New\Driver\DriverController::class)->group(function (){
            Route::post('change-language',  'changeLanguage');
        });
        Route::group(['prefix' => 'level'], function () {
            Route::get('/', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\DriverLevelController::class, 'getDriverLevelWithTrip']);
        });
        Route::group(['prefix' => 'update'], function () {
            Route::put('profile', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\DriverController::class, 'updateProfile']);
            Route::put('fcm-token', [AuthController::class, 'updateFcmToken']); //for customer and driver use AuthController
        });

        Route::get('my-activity', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\DriverController::class, 'myActivity']);
        Route::get('notification-list', [AppNotificationController::class, 'index']);

        Route::group(['prefix' => 'activity'], function () {
            Route::get('leaderboard', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\ActivityController::class, 'leaderboard']);
            Route::get('daily-income', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\ActivityController::class, 'dailyIncome']);

        });
        Route::group(['prefix' => 'loyalty-points'], function () {
            Route::get('list', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\LoyaltyPointController::class, 'index']);
            Route::post('convert', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\LoyaltyPointController::class, 'convert']);
        });

        Route::group(['prefix' => 'withdraw'], function () {
            Route::get('methods', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\WithdrawController::class, 'methods']);
            Route::post('request', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\WithdrawController::class, 'create']);
            Route::get('pending-request', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\WithdrawController::class, 'getPendingWithdrawRequests']);
            Route::get('settled-request', [\Modules\UserManagement\Http\Controllers\Api\New\Driver\WithdrawController::class, 'getSettledWithdrawRequests']);
        });

        //new controller
        Route::group(['prefix' => 'withdraw-method-info'], function () {
            Route::get('list', [WithdrawMethodInfoController::class, 'index']);
            Route::post('create', [WithdrawMethodInfoController::class, 'create']);
            Route::get('edit/{id}', [WithdrawMethodInfoController::class, 'edit']);
            Route::post('update/{id}', [WithdrawMethodInfoController::class, 'update']);
        });
    });

});

Route::post('/user/store-live-location', [UserController::class, 'storeLastLocation']);
Route::post('/user/get-live-location', [UserController::class, 'getLastLocation']);
//WebSocketsRouter::webSocket('/user/live-location', UserLocationSocketHandler::class);


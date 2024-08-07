<?php

use Illuminate\Support\Facades\Route;
use Modules\TripManagement\Http\Controllers\Api\New\Customer\TripRequestController;
use Modules\TripManagement\Http\Controllers\Api\New\Driver\TripRequestController as DriverTripController;
use Modules\TripManagement\Http\Controllers\Api\PaymentController;

/**
 * CUSTOMER API LIST
 */
//'auth:api', 'maintenance_mode'
Route::group(['prefix' => 'customer', 'middleware' => []], function () {
    Route::get('drivers-near-me', [TripRequestController::class, 'driversNearMe']);
    Route::group(['prefix' => 'ride'], function () {
        Route::controller(TripRequestController::class)->group(function () {
            Route::post('get-estimated-fare', 'getEstimatedFare'); //check -> cost is estimated according to distance
            Route::post('create', 'createRideRequest');
            Route::put('ignore-bidding', 'ignoreBidding');
            Route::get('bidding-list/{trip_request_id}', 'biddingList');
            Route::put('update-status/{trip_request_id}', 'rideStatusUpdate');
            Route::get('details/{trip_request_id}', 'rideDetails');
            Route::get('list', 'rideList');
            Route::get('final-fare', 'finalFareCalculation'); //check
            Route::post('trip-action', 'requestAction');
            Route::get('ride-resume-status', 'rideResumeStatus'); //check if the user is resuming the ride. 
            Route::put('arrival-time', 'arrivalTime');
            Route::put('coordinate-arrival', 'coordinateArrival');
            Route::put('apply-coupon', 'applyCoupon');
            Route::put('cancel-coupon', 'cancelCoupon');
            Route::get('ongoing-parcel-list', 'pendingParcelList');
            Route::get('unpaid-parcel-list', 'unpaidParcelRequest');
        });
        Route::post('track-location', [DriverTripController::class, 'trackLocation']);
        Route::get('payment', [PaymentController::class, 'payment']);
        Route::get('digital-payment', [PaymentController::class, 'digitalPayment'])->withoutMiddleware('auth:api');
    });
});


/**
 * DRIVER API LIST
 */
Route::group(['prefix' => 'driver', 'middleware' => [ 'maintenance_mode']], function () {
    Route::post('last-ride-details', [DriverTripController::class, 'lastRideDetails']);
    Route::group(['prefix' => 'ride', 'middleware' => [ 'maintenance_mode']], function () {
        Route::controller(DriverTripController::class)->group(function () {
            Route::post('bid', 'bid');
            Route::post('trip-action', 'requestAction');
            Route::put('update-status', 'rideStatusUpdate');
            Route::post('match-otp', 'matchOtp');
            Route::post('track-location', 'trackLocation');
            Route::get('details/{ride_request_id}', 'rideDetails');
            Route::get('list', 'rideList');
            Route::get('pending-ride-list', 'pendingRideList');
            Route::get('ride-resume-status', 'rideResumeStatus');
            Route::put('ride-waiting', 'rideWaiting');
            Route::put('arrival-time', 'arrivalTime');
            Route::put('coordinate-arrival', 'coordinateArrival');
            Route::get('overview', 'tripOverView');
            Route::post('ignore-trip-notification', 'ignoreTripNotification');
            Route::get('ongoing-parcel-list', 'pendingParcelList');
            Route::get('unpaid-parcel-list', 'unpaidParcelRequest');
        });
        Route::get('final-fare', [TripRequestController::class, 'finalFareCalculation']);
        Route::get('payment', [PaymentController::class, 'payment']);
    });

    Route::get('pending-ride-list-test', [DriverTripController::class, 'test']);
});

Route::post('ride/store-screenshot', [TripRequestController::class, 'storeScreenshot'])->middleware('auth:api');

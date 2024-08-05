<?php

use Illuminate\Support\Facades\Route;
use Modules\BusinessManagement\Http\Controllers\Api\Customer\ConfigController;
use Modules\BusinessManagement\Http\Controllers\Api\Driver\ConfigController as DriverConfigController;

Route::group(['prefix' => 'customer'], function () {
    Route::controller(ConfigController::class)->group(function () {
        Route::get('configuration', 'configuration');
        Route::get('pages/{page_name}', 'pages');
        Route::group(['prefix' => 'config'], function () {
            Route::get('get-zone-id', 'getZone');
            Route::get('place-api-autocomplete', 'placeApiAutocomplete');
            Route::get('distance-api', 'distanceApi');
            Route::get('place-api-details', 'placeApiDetails');
            Route::get('geocode-api', 'geocodeApi');
            Route::post('get-routes', 'getRoutes');
            Route::get('get-payment-methods', 'getPaymentMethods');
        });
    });
});


Route::group(['prefix' => 'driver'], function () {
    Route::controller(DriverConfigController::class)->group(function () {
        Route::get('configuration', 'configuration');
        Route::group(['prefix' => 'config'], function () {
            // These config will found in Customer Config
            Route::get('get-zone-id', 'getZone');
            Route::get('place-api-autocomplete', 'placeApiAutocomplete');
            Route::get('distance-api', 'distanceApi');
            Route::get('place-api-details', 'placeApiDetails');
            Route::get('geocode-api', 'geocodeApi');
            Route::get('cancellation-reason', 'cancellationReason');
        });
        Route::group(['middleware' => ['auth:api', 'maintenance_mode']], function () {
            Route::post('get-routes', 'getRoutes');
        });
    });
});

Route::group(['prefix' => 'location', 'middleware' => ['auth:api', 'maintenance_mode']], function () {
    Route::controller(ConfigController::class)->group(function () {
        Route::post('save', 'userLastLocation');
    });
});

#new route
Route::group(['prefix' => 'customer'], function () {
    Route::controller(\Modules\BusinessManagement\Http\Controllers\Api\New\Customer\ConfigController::class)->group(function () {
//        Route::get('configuration', 'configuration');
//        Route::get('pages/{page_name}', 'pages');
        Route::group(['prefix' => 'config'], function () {
//            Route::get('get-zone-id', 'getZone');
//            Route::get('place-api-autocomplete', 'placeApiAutocomplete');
//            Route::get('distance-api', 'distanceApi');
//            Route::get('place-api-details', 'placeApiDetails');
//            Route::get('geocode-api', 'geocodeApi');
//            Route::post('get-routes', 'getRoutes');
//            Route::get('get-payment-methods', 'getPaymentMethods');
            Route::get('cancellation-reason-list', 'cancellationReasonList');
        });
    });
});

Route::group(['prefix' => 'driver'], function () {
    Route::controller(\Modules\BusinessManagement\Http\Controllers\Api\New\Driver\ConfigController::class)->group(function () {
//        Route::get('configuration', 'configuration');
        Route::group(['prefix' => 'config'], function () {
            // These config will found in Customer Config
//            Route::get('get-zone-id', 'getZone');
//            Route::get('place-api-autocomplete', 'placeApiAutocomplete');
//            Route::get('distance-api', 'distanceApi');
//            Route::get('place-api-details', 'placeApiDetails');
//            Route::get('geocode-api', 'geocodeApi');
            Route::get('cancellation-reason-list', 'cancellationReasonList');
        });
        Route::group(['middleware' => ['auth:api', 'maintenance_mode']], function () {
//            Route::post('get-routes', 'getRoutes');
        });
    });
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\VehicleManagement\Http\Controllers\Api\New\Driver\VehicleBrandController;
use Modules\VehicleManagement\Http\Controllers\Api\New\Driver\VehicleCategoryController;
use Modules\VehicleManagement\Http\Controllers\Api\New\Customer\VehicleCategoryController as CustomerVehicleCategoryControllerController;
use Modules\VehicleManagement\Http\Controllers\Api\New\Driver\VehicleController;
use Modules\VehicleManagement\Http\Controllers\Api\New\Driver\VehicleModelController;



Route::group(['prefix' => 'customer'], function () {
    Route::group(['prefix' => 'vehicle', 'middleware' => ['auth:api', 'maintenance_mode']], function () {


        Route::group(['prefix' => 'category'], function () {
            Route::controller(CustomerVehicleCategoryControllerController::class)->group(function () {
                Route::get('/', 'categoryFareList');
            });
        });
    });
});


Route::group(['prefix' => 'driver'], function () {
    Route::group(['prefix' => 'vehicle', 'middleware' => ['auth:api', 'maintenance_mode']], function () {

        Route::controller(VehicleController::class)->group(function () {
            Route::post('/store', 'store');
        });
        Route::group(['prefix' => 'category'], function () {
            Route::controller(VehicleCategoryController::class)->group(function () {
                Route::get('/list', 'list');
            });
        });

        Route::group(['prefix' => 'brand'], function () {
            Route::controller(VehicleBrandController::class)->group(function () {
                Route::get('/list', 'brandList');
            });
        });

        Route::group(['prefix' => 'model'], function () {
            Route::controller(VehicleModelController::class)->group(function () {
                Route::get('/list', 'modelList');
            });
        });
    });
});

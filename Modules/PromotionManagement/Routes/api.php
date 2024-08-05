<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\PromotionManagement\Http\Controllers\Api\New\Customer\BannerSetupController;
use Modules\PromotionManagement\Http\Controllers\Api\New\Customer\CouponSetupController ;
use Modules\PromotionManagement\Http\Controllers\Api\New\Customer\DiscountSetupController;

Route::group(['prefix' => 'customer'], function (){

    Route::group(['prefix' => 'banner'], function(){
        Route::controller(BannerSetupController::class)->group(function () {
            Route::get('list', 'list');
        Route::post('update-redirection-count', 'RedirectionCount');
        });

    });
    Route::group(['prefix' => 'coupon', 'middleware' => ['auth:api', 'maintenance_mode']], function(){
        Route::controller(CouponSetupController::class)->group(function () {
            Route::get('list', 'list');
             Route::post('apply', 'apply');
        });
    });
    Route::group(['prefix' => 'discount', 'middleware' => ['auth:api', 'maintenance_mode']], function(){
        Route::controller(DiscountSetupController::class)->group(function () {
            Route::get('list', 'list');
        });
    });
});

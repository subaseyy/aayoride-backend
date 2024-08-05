<?php

use Illuminate\Support\Facades\Route;
use Modules\AuthManagement\Http\Controllers\Api\AuthController;


////customer routes
//Route::group(['prefix' => 'customer'], function () {
//    Route::group(['prefix' => 'auth'], function () {
//        Route::post('registration', [AuthController::class, 'register'])->name('customer-registration');
//        Route::post('login', [AuthController::class, 'login'])->name('customer-login');
//        Route::post('social-login', [AuthController::class, 'customerSocialLogin']);
//        //login
//        Route::post('otp-login', [AuthController::class, 'otpLogin']);
//        // reset or forget password
//        Route::post('forget-password', [AuthController::class, 'forgetPassword']);
//        Route::post('reset-password', [AuthController::class, 'resetPassword']);
//        Route::post('otp-verification', [AuthController::class, 'otpVerification']);
//        //send otp for otp login or reset
//         Route::post('send-otp', [AuthController::class, 'sendOtp']);
//
//    });
//});
//
////customer routes
//Route::group(['prefix' => 'driver'], function () {
//    Route::group(['prefix' => 'auth'], function () {
//        Route::post('registration', [AuthController::class, 'register'])->name('driver-registration');
//        Route::post('login', [AuthController::class, 'login'])->name('driver-login');
//         Route::post('send-otp', [AuthController::class, 'sendOtp']);
//        Route::post('forget-password', [AuthController::class, 'forgetPassword']);
//        Route::post('reset-password', [AuthController::class, 'resetPassword']);
//        Route::post('otp-verification', [AuthController::class, 'otpVerification']);
//    });
//});
//
//Route::group(['prefix' => 'user', 'middleware' => ['auth:api', 'maintenance_mode']], function(){
//    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
//    Route::post('change-password', [AuthController::class, 'changePassword']);
//});

#new
//customer routes
Route::controller(\Modules\AuthManagement\Http\Controllers\Api\New\AuthController::class)->group(function () {
    Route::group(['prefix' => 'customer'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('registration', 'register')->name('customer-registration');
            Route::post('login', 'login')->name('customer-login');
            Route::post('social-login', 'customerSocialLogin');
            //login
            Route::post('otp-login', 'otpLogin');
            // reset or forget password
            Route::post('forget-password', 'forgetPassword');
            Route::post('reset-password', 'resetPassword');
            Route::post('otp-verification', 'otpVerification');
            //send otp for otp login or reset
            Route::post('send-otp', 'sendOtp');

        });
    });

    //driver routes
    Route::group(['prefix' => 'driver'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('registration', 'register')->name('driver-registration');
            Route::post('login', 'login')->name('driver-login');
            Route::post('send-otp', 'sendOtp');
            Route::post('forget-password', 'forgetPassword');
            Route::post('reset-password', 'resetPassword');
            Route::post('otp-verification', 'otpVerification');
        });
    });

    Route::group(['prefix' => 'user', 'middleware' => ['auth:api', 'maintenance_mode']], function () {
        Route::post('logout', 'logout')->name('logout');
        Route::post('delete', 'delete')->name('delete');
        Route::post('change-password', 'changePassword');
    });

});

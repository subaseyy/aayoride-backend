<?php

use Illuminate\Support\Facades\Route;
use Modules\ReviewModule\Http\Controllers\Api\New\ReviewController;
Route::group(['prefix' => 'customer'], function () {
    Route::group(['prefix' => 'review', 'middleware' => ['auth:api', 'maintenance_mode']], function () {
        Route::controller(ReviewController::class)->group(function () {
            Route::get('list', 'index');
            Route::post('store', 'store');
            Route::put('check-submission', 'checkSubmission');
        });
    });
});

Route::group(['prefix' => 'driver'], function () {
    Route::group(['prefix' => 'review', 'middleware' => ['auth:api', 'maintenance_mode']], function () {
        Route::controller(ReviewController::class)->group(function () {
            Route::get('list', 'index');
            Route::post('store', 'store');
            Route::put('save/{id}', 'save');
        });
    });
});



//Route::group(['prefix' => 'customer'], function () {
//    Route::group(['prefix' => 'review', 'middleware' => ['auth:api', 'maintenance_mode']], function () {
//        Route::put('update/{id}', [ReviewController::class, 'update']);
//        Route::delete('delete/{id}', [ReviewController::class, 'destroy']);
//        Route::put('save/{id}', [ReviewController::class, 'save']);
//        Route::put('check-submission', [ReviewController::class, 'checkSubmission']);
//    });
//});
//
//Route::group(['prefix' => 'driver'], function () {
//    Route::group(['prefix' => 'review', 'middleware' => ['auth:api', 'maintenance_mode']], function () {
//        Route::put('update/{id}', [ReviewController::class, 'update']);
//        Route::delete('delete/{id}', [ReviewController::class, 'destroy']);
//        Route::put('save/{id}', [ReviewController::class, 'save']);
//        Route::put('check-submission', [ReviewController::class, 'checkSubmission']);
//    });
//});

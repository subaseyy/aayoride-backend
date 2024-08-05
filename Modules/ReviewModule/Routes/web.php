<?php

use Illuminate\Support\Facades\Route;
use Modules\ReviewModule\Http\Controllers\Web\New\Admin\ReviewController;

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {

    Route::group(['prefix' => 'driver', 'as' => 'driver.'], function () {
        Route::group(['prefix' => 'review', 'as' => 'review.'], function () {
            Route::controller(ReviewController::class)->group(function () {
                Route::get('review-export/{id}/{reviewed}', 'driverReviewExport')->name('export');
            });
        });
    });

    Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
        Route::group(['prefix' => 'review', 'as' => 'review.'], function () {
            Route::controller(ReviewController::class)->group(function () {
                Route::get('review-export/{id}/{reviewed}', 'customerReviewExport')->name('export');
            });
        });
    });

});

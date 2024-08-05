<?php

use Illuminate\Support\Facades\Route;
use Modules\TransactionManagement\Http\Controllers\Api\New\Customer\TransactionController;
use Modules\TransactionManagement\Http\Controllers\Api\New\Driver\DriverTransactionController;


Route::group(['prefix' => 'customer'], function () {

    Route::group(['prefix' => 'transaction', 'middleware' => ['auth:api', 'maintenance_mode']], function () {

        Route::controller(TransactionController::class)->group(function () {
            Route::get('list', 'list');
        });
    });
});

Route::group(['prefix' => 'driver'], function () {

    Route::group(['prefix' => 'transaction', 'middleware' => ['auth:api', 'maintenance_mode']], function () {

        Route::controller(DriverTransactionController::class)->group(function () {
            Route::get('list', 'list');
            Route::get('payable-list', 'payableTransactionHistory');
            Route::get('wallet-list', 'walletTransactionHistory');
        });
    });
});

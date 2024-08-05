<?php

use Illuminate\Support\Facades\Route;
use Modules\FareManagement\Http\Controllers\Web\Admin\ParcelFareController;
use Modules\FareManagement\Http\Controllers\Web\Admin\TripFareController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'admin', 'as'=> 'admin.', 'middleware' => 'admin'], function() {
    Route::group(['prefix' => 'fare', 'as' => 'fare.'], function () {
        Route::group(['prefix' => 'parcel', 'as'=> 'parcel.' ], function (){
            Route::get('/', [ParcelFareController::class, 'index'])->name('index');
            Route::get('create/{zone_id}', [ParcelFareController::class, 'create'])->name('create');
            Route::post('store', [ParcelFareController::class, 'store'])->name('store');
        });

        Route::group(['prefix' => 'trip', 'as'=> 'trip.' ], function (){
            Route::get('/', [TripFareController::class, 'index'])->name('index');
            Route::get('create/{zone_id}', [TripFareController::class, 'create'])->name('create');
            Route::post('store', [TripFareController::class, 'store'])->name('store');
        });
    });
});

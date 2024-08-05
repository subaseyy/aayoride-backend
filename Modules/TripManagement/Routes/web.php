<?php

use Illuminate\Support\Facades\Route;
use Modules\TripManagement\Http\Controllers\Web\TripController;

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

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    Route::group(['prefix' => 'trip', 'as' => 'trip.'], function () {
        Route::controller(TripController::class)->group(function () {
            Route::get('list/{type}', 'index')->name('index');
            Route::get('details/{id}', 'show')->name('show');
            Route::get('invoice/{id}', 'invoice')->name('invoice');
            Route::delete('delete/{id}', 'destroy')->name('delete');
            Route::get('log', 'log')->name('log');
            Route::get('export', 'export')->name('export');
            Route::get('trashed', 'trashed')->name('trashed');
            Route::get('restore/{id}', 'restore')->name('restore');
        });
    });
});

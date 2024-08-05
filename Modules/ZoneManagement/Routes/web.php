<?php

use Illuminate\Support\Facades\Route;
use Modules\ZoneManagement\Http\Controllers\Web\New\Admin\ZoneController;

//New Route Mamun
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    Route::group(['prefix' => 'zone', 'as' => 'zone.'], function () {
        Route::controller(ZoneController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('delete/{id}', 'destroy')->name('delete');
            Route::get('status', 'status')->name('status');
            Route::get('trashed', 'trashed')->name('trashed');
            Route::get('restore/{id}', 'restore')->name('restore');
            Route::delete('permanent-delete/{id}', 'permanentDelete')->name('permanent-delete');
            Route::get('get-zones', 'getZones')->name('get-zones');
            Route::get('get-coordinates/{id}', 'getCoordinates')->name('getCoordinates');
            Route::get('export', 'export')->name('export');
            Route::get('log', 'log')->name('log');
        });
    });
});

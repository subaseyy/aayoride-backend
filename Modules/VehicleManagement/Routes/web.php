<?php

use Illuminate\Support\Facades\Route;
use Modules\VehicleManagement\Http\Controllers\Web\New\Admin\VehicleBrandController;
use Modules\VehicleManagement\Http\Controllers\Web\New\Admin\VehicleCategoryController;
use Modules\VehicleManagement\Http\Controllers\Web\New\Admin\VehicleController;
use Modules\VehicleManagement\Http\Controllers\Web\New\Admin\VehicleModelController;

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


//New Route
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    Route::group(['prefix' => 'vehicle', 'as' => 'vehicle.'], function () {
        Route::controller(VehicleController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('delete/{id}', 'destroy')->name('delete');
            Route::get('status', 'status')->name('status');
            Route::get('show/{id}', 'show')->name('show');
            Route::get('log', 'log')->name('log');
            Route::get('export', 'export')->name('export');
            Route::get('trashed', 'trashed')->name('trashed');
            Route::get('restore/{id}', 'restore')->name('restore');
            Route::delete('permanent-delete/{id}', 'permanentDelete')->name('permanent-delete');
        });

        Route::group(['prefix' => 'attribute-setup', 'as' => 'attribute-setup.'], function () {
            Route::group(['prefix' => 'brand', 'as' => 'brand.'], function () {
                Route::controller(VehicleBrandController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('store', 'store')->name('store');
                    Route::get('edit/{id}', 'edit')->name('edit');
                    Route::put('update/{id}', 'update')->name('update');
                    Route::delete('delete/{id}', 'destroy')->name('delete');
                    Route::get('status', 'status')->name('status');
                    Route::get('trashed', 'trashed')->name('trashed');
                    Route::get('restore/{id}', 'restore')->name('restore');
                    Route::delete('permanent-delete/{id}', 'permanentDelete')->name('permanent-delete');
                    Route::get('log', 'log')->name('log');
                    Route::get('export', 'export')->name('export');
                    Route::get('all-brands', 'getAllAjax')->name('all-brands');
                });
            });
            Route::group(['prefix' => 'model', 'as' => 'model.'], function () {
                Route::controller(VehicleModelController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('store', 'store')->name('store');
                    Route::get('edit/{id}', 'edit')->name('edit');
                    Route::put('update/{id}', 'update')->name('update');
                    Route::delete('delete/{id}', 'destroy')->name('delete');
                    Route::get('status', 'status')->name('status');
                    Route::get('log', 'log')->name('log');
                    Route::get('export', 'export')->name('export');
                    Route::get('ajax-models/{id}', 'ajax_models')->name('ajax-models');
                    Route::get('ajax-models-child/{id}', 'ajax_models_child')->name('ajax-childes-only');
                    Route::get('trashed', 'trashed')->name('trashed');
                    Route::get('restore/{id}', 'restore')->name('restore');
                    Route::delete('permanent-delete/{id}', 'permanentDelete')->name('permanent-delete');
                });
            });
            Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
                Route::controller(VehicleCategoryController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('store', 'store')->name('store');
                    Route::get('edit/{id}', 'edit')->name('edit');
                    Route::put('update/{id}', 'update')->name('update');
                    Route::delete('delete/{id}', 'destroy')->name('delete');
                    Route::get('status', 'status')->name('status');
                    Route::get('log', 'log')->name('log');
                    Route::get('export', 'export')->name('export');
                    Route::get('all-categories', 'getAllAjax')->name('all-categories');
                    Route::get('trashed', 'trashed')->name('trashed');
                    Route::get('restore/{id}', 'restore')->name('restore');
                    Route::delete('permanent-delete/{id}', 'permanentDelete')->name('permanent-delete');
                });
            });
        });
    });
});

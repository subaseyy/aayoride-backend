<?php

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

use Illuminate\Support\Facades\Route;
//new route
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    Route::group(['prefix' => 'parcel', 'as' => 'parcel.'], function () {
        Route::group(['prefix' => 'attribute', 'as' => 'attribute.'], function () {
            Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
                Route::controller(\Modules\ParcelManagement\Http\Controllers\Web\New\Admin\ParcelCategoryController::class)->group(function (){
                    Route::get('/',  'index')->name('index');
                    Route::post('store',  'store')->name('store');
                    Route::get('edit/{id}',  'edit')->name('edit');
                    Route::put('update/{id}',  'update')->name('update');
                    Route::delete('delete/{id}',  'destroy')->name('delete');
                    Route::get('status',  'status')->name('status');
                    Route::get('download',  'download')->name('download');
                    Route::get('log',  'log')->name('log');
                    Route::get('trashed',  'trashed')->name('trashed');
                    Route::get('restore/{id}',  'restore')->name('restore');
                    Route::delete('permanent-delete/{id}',  'permanentDelete')->name('permanent-delete');
                });
            });

            Route::group(['prefix' => 'weight', 'as' => 'weight.'], function () {
                Route::controller(\Modules\ParcelManagement\Http\Controllers\Web\New\Admin\ParcelWeightController::class)->group(function (){
                    Route::get('/',  'index')->name('index');
                    Route::post('store',  'store')->name('store');
                    Route::get('edit/{id}',  'edit')->name('edit');
                    Route::put('update/{id}',  'update')->name('update');
                    Route::delete('delete/{id}',  'destroy')->name('delete');
                    Route::get('status',  'status')->name('status');
                    Route::get('download',  'download')->name('download');
                    Route::get('log',  'log')->name('log');
                    Route::get('trashed',  'trashed')->name('trashed');
                    Route::get('restore/{id}',  'restore')->name('restore');
                    Route::delete('permanent-delete/{id}',  'permanentDelete')->name('permanent-delete');
                });
            });
        });
    });
});

<?php
use Illuminate\Support\Facades\Route;
use Modules\PromotionManagement\Http\Controllers\Web\New\Admin\BannerSetupController;
use Modules\PromotionManagement\Http\Controllers\Web\New\Admin\CouponSetupController;
use Modules\PromotionManagement\Http\Controllers\Web\New\Admin\DiscountSetupController;

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



//new route
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function(){
    Route::group(['prefix' => 'promotion', 'as' => 'promotion.'], function(){
        Route::group(['prefix' => 'banner-setup', 'as' => 'banner-setup.'], function(){
            Route::controller(BannerSetupController::class)->group(function (){
                Route::get('/',  'index')->name('index');
                Route::post('store',  'store')->name('store');
                Route::get('edit/{id}',  'edit')->name('edit');
                Route::put('update/{id}',  'update')->name('update');
                Route::delete('delete/{id}',  'destroy')->name('delete');
                Route::get('status',  'status')->name('status');
                Route::get('export',  'export')->name('export');
                Route::get('log',  'log')->name('log');
                Route::get('trashed',  'trashed')->name('trashed');
                Route::get('restore/{id}',  'restore')->name('restore');
                Route::delete('permanent-delete/{id}',  'permanentDelete')->name('permanent-delete');
            });
        });
        Route::group(['prefix' => 'coupon-setup' , 'as' => 'coupon-setup.'], function (){
            Route::controller(CouponSetupController::class)->group(function (){
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('store', 'store')->name('store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::put('update/{id}', 'update')->name('update');
                Route::delete('delete/{id}', 'destroy')->name('delete');
                Route::get('status', 'status')->name('status');
                Route::get('export', 'export')->name('export');
                Route::get('log', 'log')->name('log');
                Route::get('trashed', 'trashed')->name('trashed');
                Route::get('restore/{id}', 'restore')->name('restore');
                Route::delete('permanent-delete/{id}', 'permanentDelete')->name('permanent-delete');
            });
        });
        Route::group(['prefix' => 'discount-setup' , 'as' => 'discount-setup.'], function (){
            Route::controller(DiscountSetupController::class)->group(function (){
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('store', 'store')->name('store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::put('update/{id}', 'update')->name('update');
                Route::delete('delete/{id}', 'destroy')->name('delete');
                Route::get('status', 'status')->name('status');
                Route::get('export', 'export')->name('export');
                Route::get('log', 'log')->name('log');
                Route::get('trashed', 'trashed')->name('trashed');
                Route::get('restore/{id}', 'restore')->name('restore');
                Route::delete('permanent-delete/{id}', 'permanentDelete')->name('permanent-delete');
                Route::get('test', 'test')->name('test');
            });
        });
    });
});

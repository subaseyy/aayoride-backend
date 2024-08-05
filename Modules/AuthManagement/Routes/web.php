<?php

use Illuminate\Support\Facades\Route;
use Modules\AuthManagement\Http\Controllers\Admin\Auth\LoginController;

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
//Route::group(['prefix' => 'admin', 'as'=> 'admin.' ], function() {
//    Route::group(['prefix' => 'auth', 'as'=> 'auth.' ], function() {
//        Route::get('login', [LoginController::class, 'loginView'])->name('login');
//        Route::post('login', [LoginController::class, 'login']);
//        Route::get('logout', [LoginController::class, 'logout'])->name('logout');
//    });
//});
#new route
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::controller(\Modules\AuthManagement\Http\Controllers\Web\New\Admin\Auth\LoginController::class)->group(function () {
            Route::get('login', 'loginView')->name('login');
            Route::post('login', 'login');
            Route::get('logout', 'logout')->name('logout');
        });
    });
});


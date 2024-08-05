<?php

use Illuminate\Support\Facades\Route;
use Modules\TransactionManagement\Http\Controllers\Web\Admin\Transaction\TransactionController;

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
    Route::group(['prefix' => 'transaction', 'as' => 'transaction.'], function () {
        Route::controller(\Modules\TransactionManagement\Http\Controllers\Web\New\Admin\Transaction\TransactionController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('export', 'export')->name('export');
        });
    });
});


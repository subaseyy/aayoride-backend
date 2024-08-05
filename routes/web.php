<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\PaymentRecordController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Modules\TripManagement\Entities\TripRequest;
use Pusher\Pusher;
use Pusher\PusherException;

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

Route::get('/sender', function(){
    return event(new App\Events\NewMessage("hello"));
});

Route::controller(LandingPageController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/contact-us', 'contactUs')->name('contact-us');
    Route::get('/about-us', 'aboutUs')->name('about-us');
    Route::get('/privacy', 'privacy')->name('privacy');
    Route::get('/terms', 'terms')->name('terms');
    Route::get('/test-connection', function (){
        $trip = TripRequest::first();
        try {
            checkPusherConnection(\App\Events\CustomerTripRequestEvent::broadcast($trip->driver,$trip));
        }catch(Exception $exception){

        }

    });
});
Route::get('/update-data-test',[\App\Http\Controllers\DemoController::class,'demo'])->name('demo');

Route::get('add-payment-request', [PaymentRecordController::class, 'index']);

Route::get('payment-success', [PaymentRecordController::class, 'success'])->name('payment-success');
Route::get('payment-fail', [PaymentRecordController::class, 'fail'])->name('payment-fail');
Route::get('payment-cancel', [PaymentRecordController::class, 'cancel'])->name('payment-cancel');

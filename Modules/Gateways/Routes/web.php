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

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Modules\BusinessManagement\Http\Controllers\Web\Admin\Configuration\PaymentConfigController;
use Modules\Gateways\Http\Controllers\BkashPaymentController;
use Modules\Gateways\Http\Controllers\FlutterwaveV3Controller;
use Modules\Gateways\Http\Controllers\LiqPayController;
use Modules\Gateways\Http\Controllers\MercadoPagoController;
use Modules\Gateways\Http\Controllers\PaymobController;
use Modules\Gateways\Http\Controllers\PaypalPaymentController;
use Modules\Gateways\Http\Controllers\PaystackController;
use Modules\Gateways\Http\Controllers\PaytabsController;
use Modules\Gateways\Http\Controllers\PaytmController;
use Modules\Gateways\Http\Controllers\PvitController;
use Modules\Gateways\Http\Controllers\RazorPayController;
use Modules\Gateways\Http\Controllers\SenangPayController;
use Modules\Gateways\Http\Controllers\SslCommerzPaymentController;
use Modules\Gateways\Http\Controllers\StripePaymentController;

/*

*/


Route::group(['prefix' => 'payment'], function () {

    //SSLCOMMERZ
    Route::group(['prefix' => 'sslcommerz', 'as' => 'sslcommerz.'], function () {
        Route::get('pay', [SslCommerzPaymentController::class, 'index'])->name('pay');
        Route::post('success', [SslCommerzPaymentController::class, 'success'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
        Route::post('failed', [SslCommerzPaymentController::class, 'failed'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
        Route::post('canceled', [SslCommerzPaymentController::class, 'canceled'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
    });

    //STRIPE
    Route::group(['prefix' => 'stripe', 'as' => 'stripe.'], function () {
        Route::get('pay', [StripePaymentController::class, 'index'])->name('pay');
        Route::get('token', [StripePaymentController::class, 'payment_process_3d'])->name('token');
        Route::get('success', [StripePaymentController::class, 'success'])->name('success');
    });

    //RAZOR-PAY
    Route::group(['prefix' => 'razor-pay', 'as' => 'razor-pay.'], function () {
        Route::get('pay', [RazorPayController::class, 'index']);
        Route::post('payment', [RazorPayController::class, 'payment'])->name('payment')
        ->withoutMiddleware([VerifyCsrfToken::class]);
    });

    //PAYPAL
    Route::group(['prefix' => 'paypal', 'as' => 'paypal.'], function () {
        Route::get('pay', [PaypalPaymentController::class, 'payment']);
        Route::any('success', [PaypalPaymentController::class, 'success'])->name('success')
        ->withoutMiddleware([VerifyCsrfToken::class]);;
        Route::any('cancel',  [PaypalPaymentController::class,'cancel'])->name('cancel')
        ->withoutMiddleware([VerifyCsrfToken::class]);;
    });

    //SENANG-PAY
    Route::group(['prefix' => 'senang-pay', 'as' => 'senang-pay.'], function () {
        Route::get('pay', [SenangPayController::class, 'index']);
        Route::any('callback', [SenangPayController::class, 'return_senang_pay']);
    });

    //PAYTM
    Route::group(['prefix' => 'paytm', 'as' => 'paytm.'], function () {
        Route::get('pay', [PaytmController::class, 'payment']);
        Route::any('response', [PaytmController::class, 'response'])->name('response');
    });

    //FLUTTERWAVE
    Route::group(['prefix' => 'flutterwave-v3', 'as' => 'flutterwave-v3.'], function () {
        Route::get('pay', [FlutterwaveV3Controller::class, 'initialize'])->name('pay');
        Route::get('callback', [FlutterwaveV3Controller::class, 'callback'])->name('callback');
    });

    //PAYSTACK
    Route::group(['prefix' => 'paystack', 'as' => 'paystack.'], function () {
        Route::get('pay', [PaystackController::class, 'index'])->name('pay');
        Route::post('payment', [PaystackController::class, 'redirectToGateway'])->name('payment');
        Route::get('callback', [PaystackController::class, 'handleGatewayCallback'])->name('callback');
    });

    //BKASH

    Route::group(['prefix'=>'bkash', 'as' => 'bkash.'], function () {
        // Payment Routes for bKash
        Route::get('make-payment', [BkashPaymentController::class,'make_tokenize_payment'])->name('make-payment');
        Route::any('callback', [BkashPaymentController::class,'callback'])->name('callback');
    });

    //Liqpay
    Route::group(['prefix' => 'liqpay', 'as' => 'liqpay.'], function () {
        Route::get('payment', [LiqPayController::class, 'payment'])->name('payment');
        Route::any('callback', [LiqPayController::class, 'callback'])->name('callback');
    });

    //MERCADOPAGO
    Route::group(['prefix' => 'mercadopago', 'as' => 'mercadopago.'], function () {
        Route::get('pay', [MercadoPagoController::class, 'index'])->name('index');
        Route::post('make-payment', [MercadoPagoController::class, 'make_payment'])->name('make_payment');
    });

    //PAYMOB
    Route::group(['prefix' => 'paymob', 'as' => 'paymob.'], function () {
        Route::any('pay', [PaymobController::class, 'credit'])->name('pay');
        Route::any('callback', [PaymobController::class, 'callback'])->name('callback');
    });

    //PAYTABS
    Route::group(['prefix' => 'paytabs', 'as' => 'paytabs.'], function () {
        Route::any('pay', [PaytabsController::class, 'payment'])->name('pay');
        Route::any('callback', [PaytabsController::class, 'callback'])->name('callback');
        Route::any('response', [PaytabsController::class, 'response'])->name('response');
    });

    //Pvit
    Route::group(['prefix' => 'pvit', 'as' => 'pvit.'], function () {
        Route::any('pay', [PvitController::class, 'payment'])->name('pay')
        ->withoutMiddleware([VerifyCsrfToken::class]);
        Route::any('callback', [PvitController::class,'callBack'])->name('callBack')
        ->withoutMiddleware([VerifyCsrfToken::class]);
    });
});

<?php

namespace Modules\PaymentModule\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PaymentModule\Entities\PaymentSetting;

class PaymentRecordController extends Controller
{
    private PaymentSetting $business_setting;

    public function __construct(PaymentSetting $business_setting)
    {
        $this->business_setting = $business_setting;
    }

    /**
     * Display a listing of the resource.
     * @return Application|Factory|View
     */
    public function payment_config_get(): View|Factory|Application
    {
        $dataValues = $this->business_setting->whereIn('settings_type', [PAYMENT_CONFIG])->get();
        return view('paymentmodule::admin.payment-gateway-config', compact('dataValues'));
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return RedirectResponse
     */
    public function payment_config_set(Request $request): RedirectResponse
    {
        $validation = [
            'gateway' => 'required|in:sslcommerz,paypal,stripe,razor_pay,senang_pay,paytabs,paystack,paymob,paytm,flutterwave,liqpay,bkash,mercadopago',
            'mode' => 'required|in:live,test'
        ];
        $additionalData = [];

        if ($request['gateway'] == 'sslcommerz') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'store_id' => 'required',
                'store_password' => 'required'
            ];
        } elseif ($request['gateway'] == 'paypal') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'client_id' => 'required',
                'client_secret' => 'required'
            ];
        } elseif ($request['gateway'] == 'stripe') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'api_key' => 'required',
                'published_key' => 'required',
            ];
        } elseif ($request['gateway'] == 'razor_pay') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'api_key' => 'required',
                'api_secret' => 'required'
            ];
        } elseif ($request['gateway'] == 'senang_pay') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'callback_url' => 'required',
                'secret_key' => 'required',
                'merchant_id' => 'required'
            ];
        }elseif ($request['gateway'] == 'paytabs') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'profile_id' => 'required',
                'server_key' => 'required',
                'base_url_by_region' => 'required'
            ];
        }elseif ($request['gateway'] == 'paystack') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'callback_url' => 'required',
                'public_key' => 'required',
                'secret_key' => 'required',
                'merchant_email' => 'required'
            ];
        }elseif ($request['gateway'] == 'paymob') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'callback_url' => 'required',
                'api_key' => 'required',
                'iframe_id' => 'required',
                'integration_id' => 'required',
                'hmac' => 'required'
            ];
        }elseif ($request['gateway'] == 'mercadopago') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'access_token' => 'required',
                'public_key' => 'required'
            ];
        }elseif ($request['gateway'] == 'liqpay') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'private_key' => 'required',
                'public_key' => 'required'
            ];
        }elseif ($request['gateway'] == 'flutterwave') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'secret_key' => 'required',
                'public_key' => 'required',
                'hash' => 'required'
            ];
        }elseif ($request['gateway'] == 'paytm') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'merchant_key' => 'required',
                'merchant_id' => 'required',
                'merchant_website_link' => 'required'
            ];
        }elseif ($request['gateway'] == 'bkash') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'api_key' => 'required',
                'api_secret' => 'required',
                'username' => 'required',
                'password' => 'required',
            ];
        }

        $validation = $request->validate(array_merge($validation, $additionalData));

        $this->business_setting->updateOrCreate(['key_name' => $request['gateway'], 'settings_type' => PAYMENT_CONFIG], [
            'key_name' => $request['gateway'],
            'live_values' => $validation,
            'test_values' => $validation,
            'settings_type' => PAYMENT_CONFIG,
            'mode' => $request['mode'],
            'is_active' => $request['status'],
        ]);

        return back()->with(['message'=>DEFAULT_UPDATE_200['message']]);
    }
}

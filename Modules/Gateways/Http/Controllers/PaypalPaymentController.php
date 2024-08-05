<?php

namespace Modules\Gateways\Http\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Modules\Gateways\Traits\Processor;
use Modules\Gateways\Entities\PaymentRequest;

class PaypalPaymentController extends Controller
{
    use Processor;

    private mixed $config_values;
    private string $base_url;

    private PaymentRequest $payment;

    public function __construct(PaymentRequest $payment)
    {
        $config = $this->paymentConfig('paypal', PAYMENT_CONFIG);
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
        }

        if ($config) {
            $this->base_url = ($config->mode == 'test') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
        }
        $this->payment = $payment;
    }

    public function token(): bool|string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->base_url . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, $this->config_values->client_id . ':' . $this->config_values->client_secret);

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $accessToken = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $accessToken;
    }

    /**
     * Responds with a welcome message with instructions
     *
     */
    public function payment(Request $request): JsonResponse|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->responseFormatter(GATEWAYS_DEFAULT_400, null, $this->errorProcessor($validator)), 400);
        }

        $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($data)) {
            return response()->json($this->responseFormatter(GATEWAYS_DEFAULT_204), 200);
        }

        if ($data['additional_data'] != null) {
            $business = json_decode($data['additional_data']);
            $business_name = $business->business_name ?? "my_business";
        } else {
            $business_name = "my_business";
        }

        $accessToken = json_decode($this->token(), true);

        if (isset($accessToken['access_token'])) {
            $accessToken = $accessToken['access_token'];
            $payment_data = [];
            $payment_data['purchase_units'] = [
                [
                    'reference_id' => $data->id,
                    'name' => $business_name,
                    'desc' => 'payment ID :' . $data->id,
                    'amount' => [
                        'currency_code' => $data->currency_code ?? 'USD',
                        'value' => number_format($data->payment_amount, 2)
                    ]
                ]
            ];

            $payment_data['invoice_id'] = $data->id;
            $payment_data['invoice_description'] = "Order #{$payment_data['invoice_id']} Invoice";
            $payment_data['total'] = round($data->payment_amount, 2);
            $payment_data['intent'] = 'CAPTURE';
            $payment_data['application_context'] = [
                'return_url' => route('paypal.success', ['payment_id' => $data->id]),
                'cancel_url' => route('paypal.cancel', ['payment_id' => $data->id])
            ];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->base_url . '/v2/checkout/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment_data));

            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = "Authorization: Bearer $accessToken";
            $headers[] = "Paypal-Request-Id:" . Str::uuid();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
        } else {
            return response()->json($this->responseFormatter(GATEWAYS_DEFAULT_204), 200);
        };
        $response = json_decode($response);

        $links = $response->links;
        return Redirect::away($links[1]->href);

    }

    /**
     * Responds with a welcome message with instructions
     */
    public function cancel(Request $request): Application|JsonResponse|Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        $data = $this->payment::where(['id' => $request['payment_id']])->first();
        return $this->paymentResponse($data, 'cancel');
    }

    /**
     * Responds with a welcome message with instructions
     */
    public function success(Request $request): Application|JsonResponse|Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {

        $accessToken = json_decode($this->token(), true);
        $accessToken = $accessToken['access_token'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->base_url . "/v2/checkout/orders/{$request->token}/capture");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = "Authorization: Bearer  $accessToken";
        $headers[] = 'Paypal-Request-Id:' . Str::uuid();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $response = json_decode($result);

        if ($response->status === 'COMPLETED') {
            $this->payment::where(['id' => $request['payment_id']])->update([
                'payment_method' => 'paypal',
                'is_paid' => 1,
                'transaction_id' => $response->id,
            ]);

            $data = $this->payment::where(['id' => $request['payment_id']])->first();

            if (isset($data) && function_exists($data->hook)) {
                call_user_func($data->hook, $data);
            }

            return $this->paymentResponse($data, 'success');
        }
        $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
        if (isset($payment_data) && function_exists($payment_data->hook)) {
            call_user_func($payment_data->hook, $payment_data);
        }
        return $this->paymentResponse($payment_data, 'fail');
    }
}

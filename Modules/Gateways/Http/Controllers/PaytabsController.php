<?php

namespace Modules\Gateways\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Modules\Gateways\Traits\Processor;
use Modules\Gateways\Entities\PaymentRequest;

class PaytabsController extends Controller
{
    use Processor;

    private PaymentRequest $payment;
    private User $user;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $this->payment = $payment;
        $this->user = $user;
    }

    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->responseFormatter(GATEWAYS_DEFAULT_400, null, $this->errorProcessor($validator)), 400);
        }

        $payment_data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($payment_data)) {
            return response()->json($this->responseFormatter(GATEWAYS_DEFAULT_204), 200);
        }
        $payer = json_decode($payment_data['payer_information']);

        $plugin = new AdditionalClasses\Paytabs();
        $request_url = 'payment/request';
        $data = [
            "tran_type" => "sale",
            "tran_class" => "ecom",
            "cart_id" => $payment_data->id,
            "cart_currency" => $payment_data->currency_code??"USD",
            "cart_amount" => number_format($payment_data->payment_amount, 2),
            "cart_description" => "products",
            "paypage_lang" => "en",
            "callback" => route('paytabs.callback', ['payment_id' => $payment_data->id]),
            "return" => route('paytabs.callback', ['payment_id' => $payment_data->id]),
            "customer_details" => [
                "name" => $payer->name,
                "email" => $payer->email,
                "phone" => $payer->phone ?? "000000",
                "street1" => "N/A",
                "city" => "N/A",
                "state" => "N/A",
                "country" => "N/A",
                "zip" => "00000"
            ],
            "shipping_details" => [
                "name" => "N/A",
                "email" => "N/A",
                "phone" => "N/A",
                "street1" => "N/A",
                "city" => "N/A",
                "state" => "N/A",
                "country" => "N/A",
                "zip" => "0000"
            ],
            "user_defined" => [
                "udf9" => "UDF9",
                "udf3" => "UDF3"
            ]
        ];

        $page = $plugin->send_api_request($request_url, $data);
        if (!isset($page['redirect_url'])) {
            return response()->json($this->responseFormatter(GATEWAYS_DEFAULT_204), 200);
        }
        header('Location:' . $page['redirect_url']); /* Redirect browser */
        exit();
    }

    public function callback(Request $request): Application|JsonResponse|Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        $plugin = new AdditionalClasses\Paytabs();
        $response_data = $_POST;
        $transRef = filter_input(INPUT_POST, 'tranRef');

        if (!$transRef) {
            return response()->json($this->responseFormatter(GATEWAYS_DEFAULT_204), 200);
        }

        $is_valid = $plugin->is_valid_redirect($response_data);
        if (!$is_valid) {
            return response()->json($this->responseFormatter(GATEWAYS_DEFAULT_204), 200);
        }

        $request_url = 'payment/query';
        $data = [
            "tran_ref" => $transRef
        ];
        $verify_result = $plugin->send_api_request($request_url, $data);
        $is_success = $verify_result['payment_result']['response_status'] === 'A';
        if ($is_success) {
            $this->payment::where(['id' => $request['payment_id']])->update([
                'payment_method' => 'paytabs',
                'is_paid' => 1,
                'transaction_id' => $transRef,
            ]);
            $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
            if (isset($payment_data) && function_exists($payment_data->hook)) {
                call_user_func($payment_data->hook, $payment_data);
            }
            return $this->paymentResponse($payment_data, 'success');
        }
        $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
        if (isset($payment_data) && function_exists($payment_data->hook)) {
            call_user_func($payment_data->hook, $payment_data);
        }
        return $this->paymentResponse($payment_data, 'fail');
    }

    public function response(): JsonResponse
    {
        return response()->json($this->responseFormatter(GATEWAYS_DEFAULT_200), 200);
    }
}

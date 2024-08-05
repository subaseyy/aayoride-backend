<?php

namespace Modules\Gateways\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use SimpleXMLElement;
use Illuminate\Support\Facades\Validator;
use Modules\Gateways\Traits\Processor;
use Modules\Gateways\Entities\PaymentRequest;

class PvitController extends Controller
{
    use Processor;

    private mixed $config_values;
    private $mc_tel_merchant;
    private $access_token;
    private $mc_merchant_code;
    private PaymentRequest $payment;

    public function __construct(PaymentRequest $payment)
    {
        $config = $this->paymentConfig('pvit', PAYMENT_CONFIG);

        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
        }

        if ($config) {
            $this->mc_tel_merchant = $this->config_values->mc_tel_merchant;
            $this->access_token = $this->config_values->access_token;
            $this->mc_merchant_code = $this->config_values->mc_merchant_code;
        }

        $this->payment = $payment;
    }

    public function payment(Request $req): View|Application|Factory|JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        $validator = Validator::make($req->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->responseFormatter(GATEWAYS_DEFAULT_400, null, $this->errorProcessor($validator)), 400);
        }

        $payment_data = $this->payment::where(['id' => $req['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($payment_data)) {
            return response()->json($this->responseFormatter(GATEWAYS_DEFAULT_204), 200);
        }
        $config_val = $this->config_values;
        return view('Gateways::payment.pvit', compact(['payment_data', 'config_val']));
    }

    public function callBack(Request $request): Application|JsonResponse|Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        if ($request->ref || $request->statut) {
            if ($request->statut == 200) {
                $this->payment::where(['id' => $request['payment_id']])->update([
                    'payment_method' => 'pvit',
                    'is_paid' => 1,
                    'transaction_id' => $request->ref ?? $request['payment_id'],
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

        $data = $this->payment::where(['id' => $request['payment_id']])->first();

        $data_received = file_get_contents("php://input");

        $data_received_xml = json_decode(json_encode(new SimpleXMLElement($data_received)), true);

        if (isset($data_received_xml['NUM_TRANSACTION']) && isset($data_received_xml['STATUT'])) {
            if ($data_received_xml['STATUT'] == 200) {
                $this->payment::where(['id' => $request['payment_id']])->update([
                    'payment_method' => 'pvit',
                    'is_paid' => 1,
                    'transaction_id' => $data_received_xml['NUM_TRANSACTION'],
                ]);

                $data = $this->payment::where(['id' => $request['payment_id']])->first();

                if (isset($data) && function_exists($data->hook)) {
                    call_user_func($data->hook, $data);
                }

                return $this->paymentResponse($data, 'success');
            }
            if (isset($data) && function_exists($data->hook)) {
                call_user_func($data->hook, $data);
            }
            return $this->paymentResponse($data, 'fail');
        }
        if (isset($data) && function_exists($data->hook)) {
            call_user_func($data->hook, $data);
        }
        return $this->paymentResponse($data, 'fail');
    }
}

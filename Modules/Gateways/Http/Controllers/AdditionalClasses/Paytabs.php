<?php

namespace Modules\Gateways\Http\Controllers\AdditionalClasses;

use Modules\Gateways\Traits\Processor;

class Paytabs
{
    use Processor;

    private mixed $config_values;

    public function __construct()
    {
        $config = $this->paymentConfig('paytabs', PAYMENT_CONFIG);
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
        }
    }

    function send_api_request($request_url, $data, $request_method = null)
    {
        $data['profile_id'] = $this->config_values->profile_id;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->config_values->base_url . '/' . $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_CUSTOMREQUEST => isset($request_method) ? $request_method : 'POST',
            CURLOPT_POSTFIELDS => json_encode($data, true),
            CURLOPT_HTTPHEADER => array(
                'authorization:' . $this->config_values->server_key,
                'Content-Type:application/json'
            ),
        ));

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);
        return $response;
    }

    function is_valid_redirect($post_values): bool
    {
        $serverKey = $this->config_values->server_key;
        $requestSignature = $post_values["signature"];
        unset($post_values["signature"]);
        $fields = array_filter($post_values);
        ksort($fields);
        $query = http_build_query($fields);
        $signature = hash_hmac('sha256', $query, $serverKey);
        if (hash_equals($signature, $requestSignature) === TRUE) {
            return true;
        } else {
            return false;
        }
    }
}

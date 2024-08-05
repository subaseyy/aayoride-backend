<?php

use App\Models\Order;
use Illuminate\Support\Facades\Session;
use Modules\TripManagement\Entities\TripRequest;

if (!function_exists('orderPlace')) {
    function orderPlace($data)
    {
        $order = new TripRequest();
        $order->amount = $data->payment_amount;
        $order->payment_method = $data->payment_method;
        $order->currency = $data->currency_code;
        $order->save();
    }
}


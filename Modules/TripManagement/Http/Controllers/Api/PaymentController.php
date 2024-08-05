<?php

namespace Modules\TripManagement\Http\Controllers\Api;

use App\Events\CustomerTripPaymentSuccessfulEvent;
use App\Events\DriverPaymentReceivedEvent;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\BusinessManagement\Entities\BusinessSetting;
use Modules\Gateways\Library\Payer;
use Modules\Gateways\Library\Payment as PaymentInfo;
use Modules\Gateways\Library\Receiver;
use Modules\Gateways\Traits\Payment;
use Modules\TransactionManagement\Traits\TransactionTrait;
use Modules\TripManagement\Interfaces\TripRequestInterfaces;
use Modules\UserManagement\Lib\LevelHistoryManagerTrait;
use Modules\UserManagement\Lib\LevelUpdateCheckerTrait;

class PaymentController extends Controller
{
    use TransactionTrait, Payment, LevelHistoryManagerTrait, LevelUpdateCheckerTrait;

    public function __construct(
        private TripRequestInterfaces $trip
    )
    {
    }

    /**
     * @param Request $request
     * @return Application|JsonResponse|RedirectResponse|Redirector
     */
    public function digitalPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
            'payment_method' => 'required|in:ssl_commerz,stripe,paypal,razor_pay,paystack,senang_pay,paymob_accept,flutterwave,paytm,paytabs,liqpay,mercadopago,bkash,fatoorah,xendit,amazon_pay,iyzi_pay,hyper_pay,foloosi,ccavenue,pvit,moncash,thawani,tap,viva_wallet,hubtel,maxicash,esewa,swish,momo,payfast,worldpay,sixcash,ssl_commerz,stripe,paypal,razor_pay,paystack,senang_pay,paymob_accept,flutterwave,paytm,paytabs,liqpay,mercadopago,bkash,fatoorah,xendit,amazon_pay,iyzi_pay,hyper_pay,foloosi,ccavenue,pvit,moncash,thawani,tap,viva_wallet,hubtel,maxicash,esewa,swish,momo,payfast,worldpay,sixcash'
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }
        $trip = $this->trip->getBy(column: 'id', value: $request->trip_request_id, attributes: ['relations' => ['customer.userAccount', 'fee', 'time', 'driver']]);
        if (!$trip) {
            return response()->json(responseFormatter(TRIP_REQUEST_404), 403);
        }
        if ($trip->payment_status == PAID) {

            return response()->json(responseFormatter(DEFAULT_PAID_200));
        }

        $attributes = [
            'column' => 'id',
            'payment_method' => $request->payment_method,
        ];
        $tips = $request->tips;
        $feeAttributes['tips'] = $tips;
        $attributes['tips'] = $tips;
        $trip->fee()->update($feeAttributes);
        $trip = $this->trip->update($attributes, $request->trip_request_id);
        $paymentAmount = $trip->paid_fare + $tips;
        $customer = $trip->customer;
        $payer = new Payer(
            name: $customer?->first_name,
            email: $customer->email,
            phone: $customer->phone,
            address: '');
        $additionalData = [
            'business_name' => BusinessSetting::where(['key_name' => 'business_name'])->first()->value,
            'business_logo' => asset('storage/app/public/business') . '/' . BusinessSetting::where(['key_name' => 'header_logo'])->first()->value,
        ];
//hook is look for a autoloaded function to perform action after payment
        $paymentInfo = new PaymentInfo(
            hook: 'tripRequestUpdate',
            currencyCode: businessConfig('currency_code')?->value ?? 'USD',
            paymentMethod: $request->payment_method,
            paymentPlatform: 'mono',
            payerId: $customer->id,
            receiverId: '100',
            additionalData: $additionalData,
            paymentAmount: $paymentAmount,
            externalRedirectLink: null,
            attribute: 'order',
            attributeId: $request->trip_request_id
        );
        $receiverInfo = new Receiver('receiver_name', 'example.png');
        $redirectLink = $this->generate_link($payer, $paymentInfo, $receiverInfo);

        return redirect($redirectLink);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
            'payment_method' => 'required|in:wallet,cash'
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }
        $trip = $this->trip->getBy(column: 'id', value: $request->trip_request_id, attributes: [
            'relations' => ['customer.userAccount', 'driver', 'fee']]);
        if (!$trip) {
            return response()->json(responseFormatter(TRIP_REQUEST_404), 403);
        }
        if ($trip->payment_status == PAID) {

            return response()->json(responseFormatter(DEFAULT_PAID_200));
        }

        $tips = 0;
        DB::beginTransaction();
        if (!is_null($request->tips) && $request->payment_method == 'wallet') {
            $tips = $request->tips;
        }
        $feeAttributes['tips'] = $tips;
        $attributes['tips'] = $tips;
        $attributes = [
            'column' => 'id',
            'payment_method' => $request->payment_method,
            'paid_fare' => $trip->paid_fare + $tips,
            'payment_status' => PAID
        ];

        $trip->fee()->update($feeAttributes);
        $trip = $this->trip->update($attributes, $request->trip_request_id);
        if ($request->payment_method == 'wallet') {
            if ($trip->customer->userAccount->wallet_balance < ($trip->paid_fare)) {

                return response()->json(responseFormatter(INSUFFICIENT_FUND_403), 403);
            }
            $method = '_with_wallet_balance';
            $this->walletTransaction($trip);
        } // driver only make cash payment
        elseif ($request->payment_method == 'cash') {
            $method = '_by_cash';
            $this->cashTransaction($trip);
        }

        $this->customerLevelUpdateChecker($trip->customer);
        DB::commit();

        $push = getNotification('payment_successful');
        sendDeviceNotification(
            fcm_token: auth('api')->user()->user_type == 'customer' ? $trip->driver->fcm_token : $trip->customer->fcm_token,
            title: translate($push['title']),
            description: $trip->paid_fare . ' ' . translate($push['description']) . translate($method),
            ride_request_id: $trip->id,
            type: $trip->type,
            action: 'payment_successful',
            user_id: $trip->driver->id
        );
        try {
            checkPusherConnection(DriverPaymentReceivedEvent::broadcast($trip));
        }catch(Exception $exception){

        }
        try {
            checkPusherConnection(CustomerTripPaymentSuccessfulEvent::broadcast($trip));
        }catch(Exception $exception){

        }
        if ($trip->tips > 0) {
            sendDeviceNotification(
                fcm_token: $trip->driver->fcm_token,
                title: translate('got_tipped'),
                description: translate('customer_tipped_you') . $trip->tips,
                ride_request_id: $trip->id,
                action: 'got_tipped',
                user_id: $trip->driver->id,
            );
        }

        return response()->json(responseFormatter(DEFAULT_UPDATE_200));
    }

}

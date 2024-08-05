<?php


use App\Events\CustomerTripPaymentSuccessfulEvent;
use Modules\TripManagement\Entities\TripRequest;
use Modules\TransactionManagement\Traits\TransactionTrait;
use Modules\UserManagement\Lib\LevelHistoryManagerTrait;

if (!function_exists('tripRequestUpdate'))
{
    function tripRequestUpdate($data)
    {
        $trip = TripRequest::query()
            ->with(['driver', 'customer'])
            ->find($data->attribute_id);
        $trip->paid_fare = ($trip->paid_fare +$trip->tips);
        $trip->payment_status = PAID;
        $trip->save();
        $push = getNotification('payment_successful');
        sendDeviceNotification(
            fcm_token: $trip->driver->fcm_token,
            title: translate($push['title']),
            description: translate(textVariableDataFormat(value: $push['description'],paidAmount: $trip->paid_fare,methodName: $trip->payment_method)),
            ride_request_id: $trip->id,
            type: $trip->type,
            action: 'payment_successful',
            user_id: $trip->driver->id
        );
        if ($trip->tips > 0)
        {
            $pushTips = getNotification('tips_from_customer');
            sendDeviceNotification(
                fcm_token: $trip->driver->fcm_token,
                title: translate($pushTips['title']),
                description: translate(textVariableDataFormat(value: $pushTips['description'],tipsAmount: $trip->tips)),
                ride_request_id: $trip->id,
                type: $trip->type,
                action: 'tips_from_customer',
                user_id: $trip->driver->id
            );
        }
        if (!empty($trip)) {
            try {
                event(checkPusherConnection(CustomerTripPaymentSuccessfulEvent::broadcast($trip)));
            }catch(Exception $exception){

            }
        }

        (new class {
            use TransactionTrait;
        })->digitalPaymentTransaction($trip);

        return $trip;
    }
}

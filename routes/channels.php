<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ride-request.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('message', function ($user) {
    return true;
});
#for customer app
Broadcast::channel('customer-ride-chat.{id}', \App\Broadcasting\CustomerRideChatChannel::class);
Broadcast::channel('ride-chat.{id}', \App\Broadcasting\RideChatChannel::class);
Broadcast::channel('driver-trip-accepted.{id}', \App\Broadcasting\DriverTripAcceptedChannel::class);
Broadcast::channel('driver-trip-started.{id}', \App\Broadcasting\DriverTripStartedChannel::class);
Broadcast::channel('driver-trip-cancelled.{id}', \App\Broadcasting\DriverTripCancelledChannel::class);
Broadcast::channel('driver-trip-completed.{id}', \App\Broadcasting\DriverTripCompletedChannel::class);
Broadcast::channel('driver-payment-received.{id}', \App\Broadcasting\DriverPaymentReceivedChannel::class);


#for driver app
Broadcast::channel('driver-ride-chat.{id}', \App\Broadcasting\DriverRideChatChannel::class);
Broadcast::channel('another-driver-trip-accepted.{id}.{userId}', \App\Broadcasting\AnotherDriverTripAcceptedChannel::class);
Broadcast::channel('customer-trip-cancelled-after-ongoing.{id}', \App\Broadcasting\CustomerTripCanceledAfterOngoingChannel::class);
Broadcast::channel('customer-trip-cancelled.{id}.{userId}', \App\Broadcasting\CustomerTripCanceledChannel::class);
Broadcast::channel('customer-coupon-applied.{id}', \App\Broadcasting\CustomerCouponAppliedChannel::class);
Broadcast::channel('customer-coupon-removed.{id}', \App\Broadcasting\CustomerCouponRemovedChannel::class);
Broadcast::channel('customer-trip-request.{id}', \App\Broadcasting\CustomerTripRequestChannel::class);
Broadcast::channel('customer-trip-payment-successful.{id}', \App\Broadcasting\CustomerTripPaymentSuccessfulChannel::class);

Broadcast::channel('store-driver-last-location', function ($user) {
    info("data");
    return true;
});


<?php

namespace App\Broadcasting;

use Modules\TripManagement\Entities\TempTripNotification;
use Modules\UserManagement\Entities\User;

class AnotherDriverTripAcceptedChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, $id,$userId): array|bool
    {
        return $user->id == $userId && $user->id == TempTripNotification::where(['trip_request_id'=>$id,'user_id'=>$userId])->first()->user_id;
    }
}

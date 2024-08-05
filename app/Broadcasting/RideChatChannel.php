<?php

namespace App\Broadcasting;

use Modules\TripManagement\Entities\TripRequest;
use Modules\UserManagement\Entities\User;

class RideChatChannel
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
    public function join(User $user, $id): array|bool
    {
        return ($user->id == TripRequest::find($id)->customer_id || $user->id == TripRequest::find($id)->driver_id);
    }
}

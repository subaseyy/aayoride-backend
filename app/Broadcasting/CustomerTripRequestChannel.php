<?php

namespace App\Broadcasting;


use Modules\UserManagement\Entities\User;

class CustomerTripRequestChannel
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
        return $user->id == $id;
    }
}

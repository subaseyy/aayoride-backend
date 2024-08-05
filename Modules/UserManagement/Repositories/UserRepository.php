<?php

namespace Modules\UserManagement\Repositories;

use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Interfaces\UserInterface;

class UserRepository implements UserInterface
{
    private User $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function getBy(array $attributes = []): mixed
    {
        return $this->user
            ->query()
            ->when($attributes['user_type'] ?? null, fn($query) => $query->where('user_type', $attributes['user_type']))
            ->where(['phone' => $attributes['value']])
            ->first();
    }
}

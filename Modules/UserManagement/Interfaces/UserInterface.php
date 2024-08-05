<?php

namespace Modules\UserManagement\Interfaces;

interface UserInterface
{
    /**
     * User Login Perspective
     * @param array $attributes
     * @return mixed
     */
    public function getBy(array $attributes = []): mixed;
}

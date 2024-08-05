<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\RoleUserRepositoryInterface;
use Modules\UserManagement\Service\Interface\RoleUserServiceInterface;

class RoleUserService extends BaseService implements RoleUserServiceInterface
{
    protected $roleUserRepository;

    public function __construct(RoleUserRepositoryInterface $roleUserRepository)
    {
        parent::__construct($roleUserRepository);
        $this->roleUserRepository = $roleUserRepository;
    }

    // Add your specific methods related to RoleUserService here
}

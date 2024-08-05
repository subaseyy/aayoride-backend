<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\RoleRepositoryInterface;
use Modules\UserManagement\Service\Interface\RoleServiceInterface;

class RoleService extends BaseService implements RoleServiceInterface
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        parent::__construct($roleRepository);
        $this->roleRepository = $roleRepository;
    }

    // Add your specific methods related to RoleService here
}

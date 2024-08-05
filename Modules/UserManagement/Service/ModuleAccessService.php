<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\ModuleAccessRepositoryInterface;
use Modules\UserManagement\Service\Interface\ModuleAccessServiceInterface;

class ModuleAccessService extends BaseService implements ModuleAccessServiceInterface
{
    protected $moduleAccessRepository;

    /**
     * ModuleAccessService constructor.
     *
     * @param ModuleAccessRepositoryInterface $moduleAccessRepository
     */
    public function __construct(ModuleAccessRepositoryInterface $moduleAccessRepository)
    {
        parent::__construct($moduleAccessRepository);
        $this->moduleAccessRepository = $moduleAccessRepository;
    }

    // Add your specific methods related to ModuleAccessService here
}

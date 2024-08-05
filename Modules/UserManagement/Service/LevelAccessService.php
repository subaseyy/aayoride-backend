<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\LevelAccessRepositoryInterface;
use Modules\UserManagement\Service\Interface\LevelAccessServiceInterface;

class LevelAccessService extends BaseService implements LevelAccessServiceInterface
{
    protected $levelAccessRepository;

    public function __construct(LevelAccessRepositoryInterface $levelAccessRepository)
    {
        parent::__construct($levelAccessRepository);
        $this->levelAccessRepository = $levelAccessRepository;
    }
}

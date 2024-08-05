<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\AppNotificationRepositoryInterface;
use Modules\UserManagement\Service\Interface\AppNotificationServiceInterface;

class AppNotificationService extends BaseService implements AppNotificationServiceInterface
{
    protected $appNotificationRepository;
    public function __construct(AppNotificationRepositoryInterface $appNotificationRepository)
    {
        parent::__construct($appNotificationRepository);
        $this->appNotificationRepository = $appNotificationRepository;
    }
}

<?php

namespace Modules\BusinessManagement\Service;

use App\Service\BaseService;
use Modules\BusinessManagement\Repository\FirebasePushNotificationRepositoryInterface;
use Modules\BusinessManagement\Service\Interface\FirebasePushNotificationServiceInterface;

class FirebasePushNotificationService extends BaseService implements FirebasePushNotificationServiceInterface
{
    protected $firebasePushNotificationRepository;
    public function __construct(FirebasePushNotificationRepositoryInterface $firebasePushNotificationRepository)
    {
        parent::__construct($firebasePushNotificationRepository);
        $this->firebasePushNotificationRepository = $firebasePushNotificationRepository;
    }
}

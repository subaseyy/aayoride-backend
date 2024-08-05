<?php

namespace Modules\BusinessManagement\Service;

use App\Service\BaseService;
use Illuminate\Database\Eloquent\Model;
use Modules\BusinessManagement\Repository\NotificationSettingRepositoryInterface;
use Modules\BusinessManagement\Service\Interface\NotificationSettingServiceInterface;

class NotificationSettingService extends BaseService implements NotificationSettingServiceInterface
{
    protected $notificationSettingRepository;
    public function __construct(NotificationSettingRepositoryInterface $notificationSettingRepository)
    {
        parent::__construct($notificationSettingRepository);
        $this->notificationSettingRepository = $notificationSettingRepository;
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        if ($data['type'] == 'push'){
            $updateData = [
                'push' => $data['status']
            ];
        }else{
            $updateData = [
                'email' => 0
            ];
        }
       return $this->notificationSettingRepository->update(id: $id, data: $updateData);
    }
}

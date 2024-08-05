<?php

namespace Modules\AdminModule\Service;

use App\Repository\EloquentRepositoryInterface;
use App\Service\BaseService;
use Illuminate\Database\Eloquent\Model;
use Modules\AdminModule\Repository\AdminNotificationRepositoryInterface;
use Modules\AdminModule\Service\Interface\AdminNotificationServiceInterface;

class AdminNotificationService extends BaseService implements Interface\AdminNotificationServiceInterface
{
    protected $adminNotificationRepository;

    public function __construct(AdminNotificationRepositoryInterface $adminNotificationRepository)
    {
        parent::__construct($adminNotificationRepository);
        $this->adminNotificationRepository = $adminNotificationRepository;
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        if ($id == 0) {
            $this->adminNotificationRepository->updatedBy(criteria:[],data:$data);
            $notification = $this->adminNotificationRepository->getBy(orderBy: ['created_at'=>'desc'])->first();
        } else {
            $notification = $this->adminNotificationRepository->update(id: $id, data: $data);
        }
        return $notification;
    }
}

<?php

namespace Modules\AdminModule\Service;

use App\Repository\EloquentRepositoryInterface;
use App\Service\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\AdminModule\Repository\ActivityLogRepositoryInterface;
use Modules\AdminModule\Service\Interface\ActivityLogServiceInterface;

class ActivityLogService extends BaseService implements Interface\ActivityLogServiceInterface
{
    protected $activityLogRepository;

    public function __construct(ActivityLogRepositoryInterface $activityLogRepository)
    {
        parent::__construct($activityLogRepository);
        $this->activityLogRepository = $activityLogRepository;
    }

    public function log(array $data): Collection|LengthAwarePaginator
    {
        $criteria = [
            'logable_type' => $data['logable_type']
        ];
        if (array_key_exists('user_type',$data)) {
            $criteria = array_merge($criteria, [
                'user_type' => $data['user_type']
            ]);
        }
        if (array_key_exists('logable_id',$data)) {
            $criteria = array_merge($criteria, [
                'logable_id' => $data['logable_id']
            ]);
        }
        return $this->activityLogRepository->getBy(criteria: $criteria, limit: paginationLimit(), offset: 1);
    }
}

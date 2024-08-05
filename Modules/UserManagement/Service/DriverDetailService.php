<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Repository\DriverDetailRepositoryInterface;
use Modules\UserManagement\Service\Interface\DriverDetailServiceInterface;

class DriverDetailService extends BaseService implements DriverDetailServiceInterface
{
    protected $driverDetailRepository;

    public function __construct(DriverDetailRepositoryInterface $driverDetailRepository)
    {
        parent::__construct($driverDetailRepository);
        $this->driverDetailRepository = $driverDetailRepository;
    }
    public function updateBy(array $criteria, array $data = [])
    {
        return $this->driverDetailRepository->updateBy($criteria, $data);
    }
}

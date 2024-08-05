<?php

namespace Modules\TripManagement\Service;


use App\Service\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Modules\TripManagement\Repository\FareBiddingRepositoryInterface;
use Modules\TripManagement\Service\Interface\FareBiddingServiceInterface;

class FareBiddingService extends BaseService implements FareBiddingServiceInterface
{
    protected $fareBiddingRepository;

    public function __construct(FareBiddingRepositoryInterface $fareBiddingRepository)
    {
        parent::__construct($fareBiddingRepository);
        $this->fareBiddingRepository = $fareBiddingRepository;
    }
    public function updateBy(array $criteria, array $data = []){
        $this->fareBiddingRepository->updateBy($criteria,$data);
    }
    // Add your specific methods related to FareBiddingService here


    public function getWithAvg(array $criteria = [], array $searchCriteria = [], array $whereInCriteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = [], array $withAvgRelation = [],array $whereBetweenCriteria = []): Collection|LengthAwarePaginator
    {
        return $this->fareBiddingRepository->getWithAvg( $criteria ,  $searchCriteria ,  $searchCriteria ,  $relations ,  $orderBy ,  $limit ,  $offset ,  $onlyTrashed ,  $withTrashed ,  $withCountQuery ,  $withAvgRelation );
    }
}

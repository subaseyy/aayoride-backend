<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Repository\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function loyalCustomer($loyalLevelId): Collection
    {
        return $this->model->where(['user_type' => 'customer'])
            ->where(function ($query) use ($loyalLevelId) {
                $query->whereHas('level', function ($query1) use ($loyalLevelId) {
                    $query1->where('id', $loyalLevelId);
                })->orWhereHas('level', function ($query2) use ($loyalLevelId) {
                    $query2->where('sequence', '>', function ($query3) use ($loyalLevelId) {
                        $query3->select('sequence')
                                ->from('user_levels')
                                ->where('id', $loyalLevelId);
                        });
                    });
            })
            ->get();
    }

    public function getDriverWithoutVehicle(array $criteria = [], array $searchCriteria = [], array $whereInCriteria = [], array $whereBetweenCriteria = [], array $whereHasRelations = [], array $withAvgRelations = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = [], array $appends = []): Collection|LengthAwarePaginator
    {
        $model = $this->prepareModelForRelationAndOrder(relations: $relations, orderBy: $orderBy)
            ->when(!empty($criteria), function ($whereQuery) use ($criteria) {
                $whereQuery->where($criteria);
            })->when(!empty($whereInCriteria), function ($whereInQuery) use ($whereInCriteria) {
                foreach ($whereInCriteria as $column => $values) {
        $whereInQuery->whereIn($column, $values);
    }
            })->when(!empty($whereHasRelations), function ($whereHasQuery) use ($whereHasRelations) {
                foreach ($whereHasRelations as $relation => $conditions) {
                    $whereHasQuery->whereHas($relation, function ($query) use ($conditions) {
                        $query->where($conditions);
                    });
                }
            })->when(!empty($whereBetweenCriteria), function ($whereBetweenQuery) use ($whereBetweenCriteria) {
                foreach ($whereBetweenCriteria as $column => $range) {
                    $whereBetweenQuery->whereBetween($column, $range);
                }
            })->when(!empty($searchCriteria), function ($whereQuery) use ($searchCriteria) {
                $this->searchQuery($whereQuery, $searchCriteria);
            })->when(($onlyTrashed || $withTrashed), function ($query) use ($onlyTrashed, $withTrashed) {
                $this->withOrWithOutTrashDataQuery($query, $onlyTrashed, $withTrashed);
            })
            ->when(!empty($withCountQuery), function ($query) use ($withCountQuery) {
                $this->withCountQuery($query, $withCountQuery);
            })->when(!empty($withAvgRelations), function ($query) use ($withAvgRelations) {
                foreach ($withAvgRelations as $relation) {
                    $query->withAvg($relation);
                }
            })->with(['vehicle' => fn($query) => $query->withTrashed()])
            ->whereDoesntHave('vehicle', fn($query) => $query->withTrashed());
        if ($limit) {
            return !empty($appends) ? $model->paginate($limit)->appends($appends) : $model->paginate($limit);
        }
        return $model->get();
    }
}

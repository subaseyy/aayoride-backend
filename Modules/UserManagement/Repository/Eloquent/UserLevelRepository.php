<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Entities\UserLevel;
use Modules\UserManagement\Repository\UserLevelRepositoryInterface;

class UserLevelRepository extends BaseRepository implements UserLevelRepositoryInterface
{
    public function __construct(UserLevel $model)
    {
        parent::__construct($model);
    }

    public function getStatistics(array $criteria = [], array $searchCriteria = [], array $whereInCriteria = [], array $whereBetweenCriteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator
    {
        $model = $this->prepareModelForRelationAndOrder(relations: $relations, orderBy: $orderBy)
            ->when(!empty($criteria), function ($whereQuery) use ($criteria) {
                $whereQuery->where($criteria);
            })->when(!empty($whereInCriteria), function ($whereInQuery) use ($whereInCriteria) {
                foreach ($whereInCriteria as $column => $values) {
        $whereInQuery->whereIn($column, $values);
    }
            })->when(!empty($searchCriteria), function ($whereQuery) use ($searchCriteria) {
                $this->searchQuery($whereQuery, $searchCriteria);
            })
            ->when(!empty($whereBetweenCriteria), function ($query) use($whereBetweenCriteria){
                foreach ($whereBetweenCriteria as $column => $values) {
                    // Check if both start and end values are provided for the current column
                    if (count($values) === 2 && !empty($values[0]) && !empty($values[1])) {
                        $query->whereBetween($column, $values);
                    }
                }
            })
            ->when(!empty($withCountQuery), function ($query) use ($withCountQuery) {
                $this->withCountQuery($query, $withCountQuery);
            });
        if ($limit) {
            return !empty($criteria) ? $model->paginate($limit)->appends($criteria) : $model->paginate($limit);
        }
        return $model->get();
    }
}

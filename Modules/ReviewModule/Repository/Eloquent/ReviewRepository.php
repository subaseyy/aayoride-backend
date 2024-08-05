<?php

namespace Modules\ReviewModule\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\ReviewModule\Entities\Review;
use Modules\ReviewModule\Repository\ReviewRepositoryInterface;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }


    public function getWithAvg(array $criteria = [], array $searchCriteria = [], array $whereInCriteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = [], array $withAvgRelation = [],array $whereBetweenCriteria = []): Collection|LengthAwarePaginator
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
            })->when(($onlyTrashed || $withTrashed), function ($query) use ($onlyTrashed, $withTrashed) {
                $this->withOrWithOutTrashDataQuery($query, $onlyTrashed, $withTrashed);
            })
            ->when(!empty($withCountQuery), function ($query) use ($withCountQuery) {
                $this->withCountQuery($query, $withCountQuery);
            })
            ->when(!empty($whereBetweenCriteria), function ($whereQuery) use ($whereBetweenCriteria) {
                foreach ($whereBetweenCriteria as $column => $values) {
                    $whereQuery->whereBetween($column, $values);
                }
            })
            ->when(!empty($withAvgRelation), function ($query) use ($withAvgRelation) {
                $query->withAvg($withAvgRelation[0], $withAvgRelation[1]);
            });
        if ($limit) {
            return !empty($criteria) ? $model->paginate($limit)->appends($criteria) : $model->paginate($limit);
        }
        return $model->get();
    }

}

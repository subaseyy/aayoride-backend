<?php

namespace App\Repository\Eloquent;

use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository implements EloquentRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }


//    $relations = [
//        'posts' => [ // Eager load posts with specific conditions
//            ['published', '=', true],
//            ['category_id', '=', 1],
//        ],
//        'comments', // Eager load comments without conditions
//        // Add more relations as needed
//    ];
//
//    $orderBy = [
//        'created_at' => 'desc', // Order by created_at field in descending order
//        // Add more order by clauses as needed
//    ];
    protected function prepareModelForRelationAndOrder(array $relations = [], array $orderBy = []): Model|Builder
    {
        $model = $this->model;
        if (!empty($relations)) {
            foreach ($relations as $relation => $conditions) {
                if (is_array($conditions)) {
                    $model = $model->with([$relation => function ($query) use ($conditions) {
                        foreach ($conditions as $condition) {
                            $query->where($condition[0], $condition[1], $condition[2]);
                        }
                    }]);
                } else {
                    $model = $model->with($relations);
                }
            }
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $direction) {
                $model = $model->orderBy($field, $direction);
            }
        }
        return $model;
    }

    protected function withOrWithOutTrashDataQuery($query, bool $onlyTrashed = false, bool $withTrashed = false)
    {
        if ($onlyTrashed && !$withTrashed) {
            $query->onlyTrashed();
        }
        if (!$onlyTrashed && $withTrashed) {
            $query->withTrashed();
        }
    }

    //$withCountQuery =[
    //'comments', // Just count the total comments related to each record
    //'likes', // Just count the total likes related to each record
    //'posts' => [ // Count posts with specific conditions
    //['published', '=', true],
    //['category_id', '=', 1],
    //],
    // You can add more relationships with or without conditions as needed
    //];
    protected function withCountQuery($query, array $withCountQuery = [])
    {
        foreach ($withCountQuery as $relation => $conditions) {
            if (is_array($conditions)) {
                $query->withCount([$relation => function ($query) use ($conditions) {
                    foreach ($conditions as $condition) {
                        $query->where($condition[0], $condition[1], $condition[2]);
                    }
                }]);
            } else {
                $query->withCount($relation);
            }
        }
    }
//$whereHasRelations = [
//'posts' => ['status' => 'published', 'category' => 'technology'],
//'posts.comments' => ['approved' => true],
//    // Add more relations and conditions as needed
//];

//    $searchCriteria = [
//        'relations' => [
//              'posts' => ['title', 'content'],
//                  ],
//        'fields' => ['name', 'email'], // Fields to search within
//        'value' => 'example_value', // The value to search for
//    ];
    protected function searchQuery($query, array $searchCriteria = [])
    {
        if (isset($searchCriteria['relations'])) {
            $relations = $searchCriteria['relations']; // Array of relations to search within
            $value = $searchCriteria['value'];

            $query->where(function ($query) use ($relations, $value) {
                foreach ($relations as $relation => $fields) {
                    $query->orWhereHas($relation, function ($query) use ($fields, $value) {
                        $query->where(function ($query) use ($fields, $value) {
                            foreach ($fields as $field) {
                                $query->orWhere($field, 'like', '%' . $value . '%');
                            }
                        });
                    });
                }
            });

            // Search within main model fields as well if available
            if (isset($searchCriteria['fields'])) {
                $fields = $searchCriteria['fields']; // Array of fields to search within

                foreach ($fields as $field) {
                    $query->orWhere($field, 'like', '%' . $value . '%');
                }
            }
        } elseif (isset($searchCriteria['fields'])) {
            $fields = $searchCriteria['fields']; // Array of fields to search within
            $value = $searchCriteria['value'];

            $query->where(function ($query) use ($fields, $value) {
                foreach ($fields as $field) {
                    $query->orWhere($field, 'like', '%' . $value . '%');
                }
            });
        }
    }

    public function getAll(array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = []): Collection|LengthAwarePaginator
    {
        $model = $this->prepareModelForRelationAndOrder(relations: $relations, orderBy: $orderBy)
            ->when(($onlyTrashed || $withTrashed), function ($query) use ($onlyTrashed, $withTrashed) {
                $this->withOrWithOutTrashDataQuery($query, $onlyTrashed, $withTrashed);
            })
            ->when(!empty($withCountQuery), function ($query) use ($withCountQuery) {
                $this->withCountQuery($query, $withCountQuery);
            });
        if ($limit) {
            return $model->paginate(perPage: $limit, page: $offset ?? 1);
        }
        return $model->get();
    }

    public function getBy(array $criteria = [], array $searchCriteria = [], array $whereInCriteria = [], array $whereBetweenCriteria = [], array $whereHasRelations = [], array $withAvgRelations = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = [], array $appends = []): Collection|LengthAwarePaginator
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
                    $query->withAvg($relation['relation'], $relation['column']);
                }
            });
        if ($limit) {
            return !empty($appends) ? $model->paginate(perPage: $limit, page: $offset ?? 1)->appends($appends) : $model->paginate(perPage: $limit, page: $offset ?? 1);
        }
        return $model->get();
    }

    public function create(array $data): ?Model
    {
        return $this->model->create($data);
    }

    public function createMany(array $data)
    {
        return $this->model->insert($data);
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        $resource = $this->model->find($id);
        if (is_array($data) && count($data) > 0) {
            $resource->fill($data)->save();
        }
        return $resource;
    }

    public function updatedBy(array $criteria, array $data = [],bool $withTrashed = false)
    {
        if ($withTrashed) {
            $this->model->withTrashed()->where($criteria)->update($data);
        }else{
            $this->model->where($criteria)->update($data);
        }
    }

    public function findOne(int|string $id, array $relations = [], array $withAvgRelations = [], array $withCountQuery = [], bool $withTrashed = false, bool $onlyTrashed = false): ?Model
    {
        return $this->prepareModelForRelationAndOrder(relations: $relations)
            ->when(!empty($withCountQuery), function ($query) use ($withCountQuery) {
                $this->withCountQuery($query, $withCountQuery);
            })
            ->when(($onlyTrashed || $withTrashed), function ($query) use ($onlyTrashed, $withTrashed) {
                $this->withOrWithOutTrashDataQuery($query, $onlyTrashed, $withTrashed);
            })->when(!empty($withAvgRelations), function ($query) use ($withAvgRelations) {
                foreach ($withAvgRelations as $relation) {
                    $query->withAvg($relation[0], $relation[1]);
                }
            })
            ->find($id);
    }

    public function findOneBy(array $criteria = [], array $whereInCriteria = [], array $whereBetweenCriteria = [], array $withAvgRelations = [], array $relations = [], array $withCountQuery = [], array $orderBy =[], bool $withTrashed = false, bool $onlyTrashed = false): ?Model
    {
        return $this->prepareModelForRelationAndOrder(relations: $relations)
            ->where($criteria)
            ->when(!empty($whereInCriteria), function ($whereInQuery) use ($whereInCriteria) {
                foreach ($whereInCriteria as $column => $values) {
                    $whereInQuery->whereIn($column, $values);
                }
            })
            ->when(!empty($whereBetweenCriteria), function ($whereBetweenQuery) use ($whereBetweenCriteria) {
                foreach ($whereBetweenCriteria as $column => $range) {
                    $whereBetweenQuery->whereBetween($column, $range);
                }
            })
            ->when(!empty($withCountQuery), function ($query) use ($withCountQuery) {
                $this->withCountQuery($query, $withCountQuery);
            })
            ->when(($onlyTrashed || $withTrashed), function ($query) use ($onlyTrashed, $withTrashed) {
                $this->withOrWithOutTrashDataQuery($query, $onlyTrashed, $withTrashed);
            })->when(!empty($withAvgRelations), function ($query) use ($withAvgRelations) {
                foreach ($withAvgRelations as $relation) {
                    $query->withAvg($relation[0], $relation[1]);
                }
            })->when(!empty($orderBy), function ($query) use ($orderBy) {
                foreach ($orderBy as $column => $order) {
                    $query->orderBy($column, $order);
                }
            })
            ->first();
    }

    public function delete(int|string $id): bool
    {
        return $this->model
            ->find($id)
            ->delete();
    }

    public function deleteBy(array $criteria): bool
    {
        return $this->model
            ->where($criteria)
            ->delete();
    }

    public function permanentDelete(int|string $id): bool
    {
        return $this->model->withTrashed()
            ->find($id)
            ->forceDelete();
    }

    public function permanentDeleteBy(array $criteria): bool
    {
        return $this->model->withTrashed()
            ->where($criteria)
            ->forceDelete();
    }

    public function restoreData(int|string $id): Mixed
    {
        return $this->model->onlyTrashed()
            ->find($id)->restore();
    }
}

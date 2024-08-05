<?php

namespace App\Repository;

use App\Exceptions\Repository\RepositoryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;

interface EloquentRepositoryInterface
{
    public function getAll(array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = []): Collection|LengthAwarePaginator;

    public function getBy(array $criteria = [], array $searchCriteria = [], array $whereInCriteria = [], array $whereBetweenCriteria = [], array $whereHasRelations = [], array $withAvgRelations = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false, array $withCountQuery = [], array $appends = []): Collection|LengthAwarePaginator;

    public function create(array $data): ?Model;

    public function createMany(array $data);

    public function update(string|int $id, array $data = []): ?Model;

    public function updatedBy(array $criteria, array $data = [],bool $withTrashed = false);

    public function findOne(string|int $id, array $relations = [], array $withAvgRelations = [], array $withCountQuery = [], bool $withTrashed = false, bool $onlyTrashed = false): ?Model;

    public function findOneBy(array $criteria = [], array $whereInCriteria = [], array $whereBetweenCriteria = [], array $withAvgRelations = [], array $relations = [], array $withCountQuery = [], array $orderBy =[], bool $withTrashed = false, bool $onlyTrashed = false): ?Model;

    public function delete(string|int $id): bool;

    public function deleteBy(array $criteria): bool;

    public function permanentDelete(string|int $id): bool;

    public function permanentDeleteBy(array $criteria): bool;

    public function restoreData(string|int $id): Mixed;
}

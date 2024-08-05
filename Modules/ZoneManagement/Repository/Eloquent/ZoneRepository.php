<?php

namespace Modules\ZoneManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\ZoneManagement\Entities\Zone;
use Modules\ZoneManagement\Repository\ZoneRepositoryInterface;

class ZoneRepository extends BaseRepository implements ZoneRepositoryInterface
{
    public function __construct(Zone $model)
    {
        parent::__construct($model);
    }


    public function getByPoints($point)
    {
        return $this->model->contains('coordinates', $point);
    }

    public function findOne($id, array $relations = [], array $withAvgRelations = [], array $withCountQuery = [], bool $withTrashed = false, bool $onlyTrashed = false): ?Model
    {
        return $this->prepareModelForRelationAndOrder(relations: $relations)
            ->selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")
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
}

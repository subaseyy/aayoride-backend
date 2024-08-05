<?php

namespace Modules\BusinessManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\BusinessManagement\Entities\CancellationReason;
use Modules\BusinessManagement\Repository\CancellationReasonRepositoryInterface;

class CancellationReasonRepository extends BaseRepository implements CancellationReasonRepositoryInterface
{
    public function __construct(CancellationReason $model)
    {
        parent::__construct($model);
    }
}

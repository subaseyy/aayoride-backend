<?php

namespace Modules\ParcelManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\ParcelManagement\Entities\ParcelCategory;
use Modules\ParcelManagement\Repository\ParcelCategoryRepositoryInterface;

class ParcelCategoryRepository extends BaseRepository implements ParcelCategoryRepositoryInterface
{
    public function __construct(ParcelCategory $model)
    {
        parent::__construct($model);
    }
}

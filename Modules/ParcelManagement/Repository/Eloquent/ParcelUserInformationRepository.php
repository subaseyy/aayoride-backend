<?php

namespace Modules\ParcelManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\ParcelManagement\Entities\ParcelUserInfomation;
use Modules\ParcelManagement\Repository\ParcelUserInformationRepositoryInterface;

class ParcelUserInformationRepository extends BaseRepository implements ParcelUserInformationRepositoryInterface
{
    public function __construct(ParcelUserInfomation $model)
    {
        parent::__construct($model);
    }
}

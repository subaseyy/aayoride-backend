<?php

namespace Modules\ParcelManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\ParcelManagement\Entities\ParcelInformation;
use Modules\ParcelManagement\Repository\ParcelUserInformationRepositoryInterface;

class ParcelInformationRepository extends BaseRepository implements ParcelUserInformationRepositoryInterface
{
    public function __construct(ParcelInformation $model)
    {
        parent::__construct($model);
    }
}

<?php

namespace Modules\UserManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\OtpVerification;
use Modules\UserManagement\Repository\OtpVerificationRepositoryInterface;

class OtpVerificationRepository extends BaseRepository implements OtpVerificationRepositoryInterface
{
    public function __construct(OtpVerification $model)
    {
        parent::__construct($model);
    }
}
